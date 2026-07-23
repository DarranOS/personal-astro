---
title: "Jarilo Debug Mode"
subtitle: "Title 2 Title 2 Title 2"
tags: ["Wordpress"]
poster: "./images/image-3.webp"
---

# Notes from building Jarilo Debug Mode

I keep a running list of "things I had to learn the hard way" whenever I build something, mostly so future-me doesn't have to relearn them. This one got long enough to be a blog post. It's about a WordPress plugin I built called Jarilo Debug Mode — a pre-launch QA overlay for the Elementor sites we ship at the agency — and the handful of dumb, specific, very findable-on-Stack-Overflow-in-hindsight problems that came with it.

Nothing here is groundbreaking. If you've done browser-side tooling for a while, you've probably hit at least two of these already. Consider it a "so you don't have to" post.

## Why this exists at all

Pre-launch QA on client sites was one of those tasks that's individually trivial and collectively exhausting. Is every heading tag semantically correct? Are any images oversized? Do all the links actually go somewhere? Is the contact form still wired to the right email, or is it quietly still pointed at a dev inbox from three months ago? Did staging's `noindex` tag survive into production?

Any one of these takes ten seconds to check. Across a twenty-page site, checked by hand, every single time, they add up to an afternoon of squinting — and it's exactly the kind of repetitive attention-checking task where humans get worse the more times they do it, not better.

## What it turned into

It started as a single CSS file that color-coded heading tags and put outlines around empty links — the kind of thing you paste into dev tools and forget about. It did not stay that small. It's now a full plugin with six independently toggleable overlay categories (headings, links, images, forms, accessibility, misc), an admin page for configuring approved contact info and link rules, six CSV-exporting full-site audits with run-over-run history, real WCAG 2.2 contrast and touch-target math, and — the part I'm most pleased with — it works inside the Elementor editor itself, not just on the live front end.

I'm not going to walk through the feature list like a changelog, though, because the interesting part was never the list. It was the specific ways each piece broke before it worked.

## The problems, in the order I hit them

**Pseudo-elements don't render on replaced elements.** I wanted to badge empty links and show an input's type via `::before`/`::after`. Turns out `<input>` and `<img>` are "replaced elements" per the CSS spec, and pseudo-elements just don't render on them at all. Not a bug — a genuinely correct platform behavior I'd never had reason to learn before. For images, the fix was wrapping the image in a runtime-injected `<span>` and hosting the badge there instead. For form inputs, `:has()` on the wrapping field container let me detect the input's type and badge the wrapper.

**Every element only gets two pseudo-element slots.** This one I found by accident. As I kept adding page-level fixed badges — a noindex warning, a missing-`<html lang>` warning, a no-H1-on-page warning — they all wanted to live on `<html>`, and `<html>` only has `::before` and `::after` to give out. Once I had a third badge, something silently stopped showing. The fix was mildly humbling: stop trying to be clever with CSS and just inject real DOM elements via JS. No slot limit, scales to as many badges as I want, and honestly should have been the plan from the start.

**A link checker that never clicks anything still logged me out.** I built an automated checker using `fetch()` HEAD requests, on the theory that HEAD requests are inert — no clicking, no side effects, totally safe to fire at every link on the page. Except one of those links was the WordPress admin bar's own "Log out" link, which is a nonce-bearing action URL. WordPress doesn't distinguish HEAD from GET for that URL. Visiting it _is_ logging out. So the checker logged itself out mid-scan, which is a very confusing failure mode to debug until you realize what happened. Fix: explicitly exclude wp-admin/wp-login URLs and anything carrying a nonce or a known WooCommerce action param (`_wpnonce=`, `remove_item=`, `add-to-cart=`) from automatic checking. "Just check every link" turned out to have some very sharp edges.

**Browsers queue their own connections, and that lied to my timeouts.** On a big product category page with lots of links, most checks reported timing out at 8 seconds — but the server was fine. Browsers cap concurrent connections per host at around six. Firing 40+ fetches at once meant most of them just sat queued client-side, burning their timeout before they'd even been sent. The fix was a small concurrency-limited queue, four in flight at a time, so a check's timer only starts once it's actually dispatched. Obvious once you see it. Not obvious while you're staring at a healthy server wondering why everything's timing out.

**The classic iframe bug, right on schedule.** The site audit feature loads every page in a hidden iframe and reads what the on-page scanners have already tagged. Every page came back empty. The cause was one I'd half-remembered from somewhere and fully forgotten how to avoid: appending an iframe to the DOM before setting its `src` briefly loads `about:blank`, which fires its own `load` event. My code saw a "loaded" page, decided it was settled, and read an empty document before the real page ever arrived. Fix is one line — set `src` before insertion — plus a defensive check to ignore any leftover blank-page load event. I will probably make this exact mistake again in three years.

**Some data just isn't in the page, and that's fine.** I wanted to show which email address each Elementor contact form actually sends to. Elementor never puts that in the rendered HTML — for good reason, since it would leak internal routing to anyone who views source. It only exists in the page's saved Elementor config, in the database. Being an actual plugin rather than a bookmarklet or browser extension, I could just read it server-side and hand it to the front end. A small but satisfying reminder that some walls only exist because you're on the wrong side of them.

**Reuse over reimplementation.** Rather than write a second, parallel PHP crawler for the full-site audit, it loads each page in a same-origin hidden iframe (which inherits the logged-in session), lets the exact same client-side scanners that run on every normal page view do their thing, waits for them to settle, and reads the DOM classes they've already tagged. Less code, and it guarantees the audit report and the live overlay can never quietly disagree with each other.

**Testing against a real site found what design review didn't.** Running the finished image-size scanner against an actual staging site turned up a background image applied via CSS `::before` that wasn't being flagged. `getComputedStyle(element)` doesn't see pseudo-element styles — you need `getComputedStyle(element, '::before')` explicitly. Nothing about this was surprising once I saw it. It just hadn't occurred to me to look.

## What it still can't do

I tried not to oversell this to myself, which is a thing I have to actively watch for when a tool starts working well. Cross-origin link status can't be read at all because of CORS — a `no-cors` request confirms _something_ answered but hides the actual status code, so external links get labeled "unverified" rather than a false-positive "OK." Contrast checking skips anything sitting on a background image, because there's no reliable way to sample a photo's pixel color from CSS. And the accessibility checks are explicitly framed as catching common issues, not replacing a real audit — automated tooling generally covers something like 30–40% of WCAG, and the rest genuinely needs a human with a keyboard and a screen reader.

None of that feels like a shortcoming worth hiding. It's closer to the actual shape of the problem: a lot of pre-launch QA is mechanical enough to automate, and a meaningful chunk of it just isn't, and knowing which parts are which is most of the design work.

---

# Code Snippets — Jarilo Debug Mode

Real excerpts pulled directly from the plugin's source, trimmed down and
annotated. Numbered to match the `<technical_stories_worth_telling>`
section of the blog prompt, so it's easy to drop the matching snippet
next to the story it illustrates.

---

### 1. Working around pseudo-elements not rendering on `<img>`

`::before`/`::after` never render on replaced elements. Fix: wrap the
image in a real element at runtime and host the badge there instead.

```js
function wrapImage(img, classes, note, dimsNote) {
  var wrap = document.createElement("span");
  wrap.className = "jrd-img-wrap " + classes.join(" ");
  if (note) wrap.setAttribute("data-jrd-img-note", note);
  if (dimsNote) wrap.setAttribute("data-jrd-dims-note", dimsNote);
  img.classList.add.apply(img.classList, classes);
  img.title = [note, dimsNote].filter(Boolean).join(" | ");

  img.parentNode.insertBefore(wrap, img);
  wrap.appendChild(img);
}
```

---

### 3. The link checker that logged itself out

Skip list built after the checker triggered a real WordPress logout by
HEAD-requesting the admin bar's own "Log out" link — visiting that URL
_is_ the action, and WordPress doesn't distinguish HEAD from GET for it.

```js
// Matched against the resolved absolute URL — covers admin/login pages
// and any link that carries a nonce or a known WooCommerce action
// parameter, since those exist to DO something, not to be visited as
// a normal destination.
var SKIP_IF_CONTAINS = [
  "/wp-admin",
  "/wp-login.php",
  "_wpnonce=",
  "remove_item=",
  "add-to-cart=",
  "wc-ajax=",
];
```

---

### 4. Staggering requests so the browser's own connection queue doesn't eat the timeout

Firing 40+ `fetch()` calls at once meant most sat queued behind the
browser's ~6-connections-per-host limit, burning their timeout before
ever being sent — looked exactly like a flood of server timeouts. Fixed
with a tiny concurrency-limited queue:

```js
function pump() {
  while (activeCount < MAX_CONCURRENT && queue.length) {
    processNext(queue.shift());
  }
}

function processNext(item) {
  activeCount++;
  checkUrl(item.absolute, item.sameOrigin).then(function (result) {
    activeCount--;
    item.resolve(result);
    pump();
  });
}

function enqueueCheck(absolute, sameOrigin) {
  if (cache[absolute]) return cache[absolute];

  var promise = new Promise(function (resolve) {
    queue.push({
      absolute: absolute,
      sameOrigin: sameOrigin,
      resolve: resolve,
    });
  });
  cache[absolute] = promise;
  pump();
  return promise;
}
```

---

### 5. The classic dynamically-created-iframe bug

Every audited page came back with zero results. Cause: appending an
iframe _before_ setting its `src` briefly loads `about:blank` first,
which fires its own `load` event — and a blank page obviously has
nothing "in progress," so the code declared it settled and read an
empty document before the real page ever loaded.

```js
// Setting src BEFORE inserting into the document avoids the classic
// bug where an appended-then-navigated iframe briefly loads about:blank
// first and fires its own load event.
iframe.src = pageInfo.url;
document.body.appendChild(iframe);
```

---

### 6. Data that can't be read from the rendered page at all

Elementor never outputs a form's "send to" email address to the HTML —
deliberately, since that would leak internal contact routing to anyone
who views source. Only place it exists is the page's saved Elementor
configuration in the database, which — because this is a real WordPress
plugin, not just a browser script — can be read directly:

```php
// Best-effort guesses at Elementor Pro's Form widget "Email" action
// settings keys — these shift between versions and can't be verified
// without a live install. Anything not in this list but still
// containing "email" falls through so the real key can be spotted if
// a guess here is wrong.
$known_email_fields = array(
	'email_to'        => 'email_to',
	'email_from'      => 'from',
	'email_from_name' => 'from_name',
	'email_reply_to'  => 'reply_to',
	'email_cc'        => 'cc',
	'email_bcc'       => 'bcc',
	'email_subject'   => 'subject',
);
```

---

### 8. `getComputedStyle()` misses pseudo-element backgrounds

A background image scanner kept missing images that Elementor had
applied via a `::before` pseudo-element rather than the element itself —
`getComputedStyle(element)` alone only sees the element's own styles.

```js
function findBackgroundUrl(el) {
  var sources = [null, "::before", "::after"];
  for (var s = 0; s < sources.length; s++) {
    var bg = sources[s]
      ? getComputedStyle(el, sources[s]).backgroundImage
      : getComputedStyle(el).backgroundImage;
    if (!bg || bg === "none") continue;

    var match = URL_RE.exec(bg);
    if (match) {
      return { url: match[1], pseudo: sources[s] };
    }
  }
  return null;
}
```

---

### 9a. Real WCAG contrast math, not an approximation

```js
function relLuminance(c) {
  function lin(v) {
    v = v / 255;
    return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
  }
  return 0.2126 * lin(c.r) + 0.7152 * lin(c.g) + 0.0722 * lin(c.b);
}

function contrastRatio(c1, c2) {
  var l1 = relLuminance(c1) + 0.05;
  var l2 = relLuminance(c2) + 0.05;
  return l1 > l2 ? l1 / l2 : l2 / l1;
}
```

### 9b. Honestly labeling what browser security hides

A `no-cors` cross-origin request confirms the browser reached _something_
but hides the actual HTTP status entirely — no way around it, so external
links get labeled accordingly rather than falsely marked OK:

```js
if (!sameOrigin) {
  return {
    tier: "unverified",
    note: "external — reachable, status unverifiable",
  };
}
```
