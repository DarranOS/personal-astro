---
layout: ../layouts/Blog.astro
title: "Work Summary"
subtitle: "Title 2 Title 2 Title 2 - My Jobs"
poster: "/images/darran-o-shea.jpg"
---

# Web Typography Cheatsheet

A quick reference based on notes from _Web Typography_ by Richard Rutter.

## Pixels

A pixel used to be a hardware pixel — the smallest dot a screen could produce. With the arrival of high-density displays like Apple's Retina, the CSS pixel was redefined as a **reference pixel**: a measure of distance equal to 1/96 of an inch, based on the visual angle of one pixel on a 96 dpi device viewed at arm's length (~28 inches).

In practice: CSS pixels are no longer individual physical picture elements, but a unit of distance. High-resolution screens map multiple hardware pixels to each CSS pixel to maintain consistent sizing.

## Spaces

The regular space (U+0020) typically separates words by about 0.25 em, though there's no standard size — type designers specify space width per font.

| Space                 | Width           | HTML                            | Use                                    |
| --------------------- | --------------- | ------------------------------- | -------------------------------------- |
| Regular space         | ~0.25 em        | (spacebar)                      | Separating words                       |
| Non-breaking space    | Same as regular | `&nbsp;`                        | Keep words together on one line        |
| Narrow no-break space | Narrow          | `&#8239;`                       | Between initials, or numbers and units |
| Thin space            | 1/6 em          | `&thinsp;`                      | Separating nested quotation marks      |
| Hair space            | 1/24 em         | `&#8202;` or `&hairsp;` (HTML5) | Prevent characters touching            |

### When to use each

- **Non-breaking space** (`&nbsp;`): Use in phrases like "Page&nbsp;2" to prevent the 2 dropping to a new line. Also between initials and surnames (D.&nbsp;H.&nbsp;Lawrence), and in dates (14&nbsp;March).
- **Narrow no-break space**: Between numbers and their units, or between initials if you need slightly less space than a regular non-breaking space.
- **Thin space**: Separating nested quotation marks, e.g. _Looking up, he said, 'She mouthed "I love you"&thinsp;'_
- **Hair space**: Preventing adjacent characters from touching without providing significant separation.

## Dashes and hyphens

A hyphen is **not** a dash. Use the right symbol for the job.

### Hyphen (-)

Directly accessible from the keyboard. Four legitimate uses:

1. Joining words with combined meaning: _pick-me-up, government-mandated, Berners-Lee_
2. Indicating missing words in compounds: _the short- and long-term effects_
3. Indicating stuttering speech: _'p-p-please'_
4. Splitting words across lines

### En dash (–)

1 en long (half an em). HTML entity: `&ndash;`

Use for numerical ranges and connections:

- 4–5 minutes
- 28 March – 3 April
- copyright 2005–2016
- 87–135 Brompton Road

### Em dash (—)

1 em long. HTML entity: `&mdash;`

Used for parenthetical breaks in sentences. Spacing around em dashes is a style choice:

- Normal space: `this and that — that and this`
- Thin space: `this and that&thinsp;—&thinsp;that and this`
- Hair space: `this and that&hairsp;—&hairsp;that and this`
- No space: `this and this—that and this`

### Minus sign (−)

Often shorter than an en dash and sits higher in the line. HTML: `&minus;`

## Quotation marks

Use proper curly quotes rather than straight typewriter quotes.

| Mark         | HTML              |
| ------------ | ----------------- |
| " " (double) | `&ldquo; &rdquo;` |
| ' ' (single) | `&lsquo; &rsquo;` |

## Mathematical and technical symbols

Don't substitute keyboard characters for proper typographic symbols.

| Symbol | Name              | HTML       | Example                               |
| ------ | ----------------- | ---------- | ------------------------------------- |
| ×      | Multiplication    | `&times;`  | 4 × 4 vehicles; A4 is 210 × 297 mm    |
| ÷      | Division (obelus) | `&divide;` | Use instead of / for "divided by"     |
| ′      | Prime             | `&prime;`  | Feet, minutes of arc: 6′ 4″           |
| ″      | Double prime      | `&Prime;`  | Inches, seconds of arc                |
| °      | Degree            | `&deg;`    | 50°50′35″N                            |
| …      | Ellipsis          | `&hellip;` | Use instead of three full stops       |
| &      | Ampersand         | `&amp;`    | Required in HTML (reserved character) |

**Ampersands** are especially useful where space is tight — tables, charts, captions.

**Primes** should be used for feet/inches and minutes/seconds, not straight quotes.

## Symbol entry reference

| Symbol | Name               | Windows      | Mac         | iOS            | HTML       |
| ------ | ------------------ | ------------ | ----------- | -------------- | ---------- |
| (hair) | Hair space         | `alt + 200A` | —           | —              | `&#8202;`  |
| (thin) | Thin space         | `alt + 2009` | —           | —              | `&thinsp;` |
| –      | En dash            | `alt + 0150` | `⌥ + -`     | `123 → hold -` | `&ndash;`  |
| —      | Em dash            | `alt + 0151` | `⇧ + ⌥ + -` | `123 → hold -` | `&mdash;`  |
| −      | Minus              | —            | —           | `→ !?#`        | `&minus;`  |
| ×      | Multiply           | `alt + 0215` | —           | `→ !?#`        | `&times;`  |
| ÷      | Divide             | `alt + 0247` | `⌥ + /`     | `→ !?#`        | `&divide;` |
| '      | Left single quote  | `alt + 0145` | `⌥ + ]`     | `123 → hold '` | `&lsquo;`  |
| '      | Right single quote | `alt + 0146` | `⇧ + ⌥ + ]` | `123 → hold '` | `&rsquo;`  |
| "      | Left double quote  | `alt + 0147` | `⌥ + [`     | `123 → hold "` | `&ldquo;`  |
| "      | Right double quote | `alt + 0148` | `⇧ + ⌥ + [` | `123 → hold "` | `&rdquo;`  |
| &      | Ampersand          | `⇧ + 7`      | `⇧ + 7`     | `123 → &`      | `&amp;`    |
| …      | Ellipsis           | `alt + 0133` | `⌥ + ;`     | `123 → hold .` | `&hellip;` |
| ′      | Single prime       | —            | —           | —              | `&prime;`  |
| ″      | Double prime       | —            | —           | —              | `&Prime;`  |
| °      | Degree             | `alt + 0176` | `⇧ + ⌥ + 8` | `123 → hold 0` | `&deg;`    |
| ·      | Middle dot         | `alt + 0183` | `⇧ + ⌥ + 9` | `123 → hold -` | `&middot;` |
| •      | Bullet             | `alt + 0149` | `⌥ + 8`     | `123 → hold -` | `&bull;`   |
| ©      | Copyright          | `alt + 0169` | `⌥ + g`     | `→ !?#`        | `&copy;`   |
| ®      | Registered         | `alt + 0174` | `⌥ + r`     | `→ !?#`        | `&reg;`    |
| ™      | Trademark          | `alt + 0153` | `⌥ + 2`     | `→ !?#`        | `&trade;`  |

## Ligatures

OpenType supports programmable substitutions — for example, an `f` followed by an `i` can be replaced automatically with an `ﬁ` glyph.

Control ligatures with `font-variant-ligatures`:

```css
/* Turn on common ligatures */
body {
  font-variant-ligatures: common-ligatures;
}

/* Turn off common ligatures */
body {
  font-variant-ligatures: no-common-ligatures;
}

/* Turn on discretionary and historical ligatures (good for display text) */
h1 {
  font-variant-ligatures: discretionary-ligatures historical-ligatures;
}
```

## Capitals and small caps

The term _uppercase_ comes from letterpress printing, where capital letters were kept in a separate case above the minuscule sorts.

For strings of capitals like NASA or UNESCO, small caps let you include them without calling unwarranted attention.

```css
/* Replace both upper and lowercase letters with small caps */
abbr.smallcaps {
  font-variant-caps: all-small-caps;
}
```

If you need to shout, SHOUT in full caps — but reach for small caps when you just need acronyms to sit quietly in the text.

## Hierarchy

Size isn't the only tool for establishing hierarchy. Consider also:

- Weight
- Style (italics, capitalisation)
- Typeface
- Colour
- Spacing and proximity

The most important elements don't have to be the largest — they need to be the most distinguished. In other words, they need the most contrast.

## Text sizes: three kinds

Choose your smallest size first, then work up.

- **Reference (small)** — small print, notes, bibliographies, dictionaries, data. Not read immersively, so can be set smaller.
- **Reading (medium)** — immersive reading text. Includes headings that may need sizing to differentiate from body text.
- **Display (large)** — intended to be looked at before it is read. Makes an impact, evokes emotion, sets tone.

## Modular scales

A modular scale builds sizes around a ratio. The golden ratio (φ ≈ 1.618) is one popular choice, though many others exist.

### Example responsive type scale

```css
h1 {
  font-size: 2.0625rem; /* 33px */
}
h2 {
  font-size: 1.5625rem; /* 25px */
}
h3 {
  font-size: 1.3125rem; /* 21px */
}
h4 {
  font-size: 1rem; /* 16px */
}
p {
  font-size: 1rem; /* 16px */
}

@media screen and (min-width: 60em) and (min-height: 30em) {
  h1 {
    font-size: 3.1875rem; /* 51px */
  }
  h2 {
    font-size: 2.0625rem; /* 33px */
  }
  h3 {
    font-size: 1.5625rem; /* 25px */
  }
  h4 {
    font-size: 1.125rem; /* 18px */
  }
  p {
    font-size: 1.125rem; /* 18px */
  }
}

@media screen and (min-width: 120em) and (min-height: 60em) {
  h1 {
    font-size: 4.875rem; /* 78px */
  }
  h2 {
    font-size: 2.75rem; /* 44px */
  }
  h3 {
    font-size: 1.75rem; /* 28px */
  }
  h4 {
    font-size: 1.3125rem; /* 21px */
  }
  p {
    font-size: 1.3125rem; /* 21px */
  }
}
```

Note the `min-height` feature in the media queries — there's no point in bumping up to display-sized type on a wide but short viewport. Responsive design should account for both dimensions.
