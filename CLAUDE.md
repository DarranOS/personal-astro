# Building my personal site in Astro

I'm building a personal site as a calling card, aimed primarily at senior hiring managers at product companies for potential Head of Delivery inbound. Secondary audience: peers, collaborators, occasional freelance enquiry. I'm building it in Astro 6 and want your help implementing it.

## Context

- @context/current-feature.md
- @context/ai-interaction.md

## About me

I'm Darran O'Shea, Technical Delivery Lead at Jarilo Design, a small web agency. I came up through hospitality (a decade running bars and restaurants, managing teams of 50+), taught myself to code during lockdown via The Odin Project, joined Jarilo as a Web Developer in 2022, and was promoted to Technical Operations Lead in 2024. I now oversee delivery of 350+ projects a year across design, development, QA, account management, and consultancy. Background also includes an animation degree, so I have real 3D chops (Three.js, Blender).

## Key proof points to feature:

350+ projects delivered annually
41% team output increase from AI adoption
20% reduction in project timelines
90+ projects shipped as a developer before promotion

## Positioning

The site should position me as a technical delivery leader who came up through the code and still ships it — not as a generalist developer, not as a freelancer. Distinctive angle is the unusual arc (hospitality → self-taught dev → delivery lead) and the AI-augmented delivery work. Explicitly not pitching 3D freelance — the visual work lives on the site as range-signalling, not as a hire-me CTA.
Site structure
Six surfaces total:

Home — single scrolling page with hero, proof strip, selected work, about preview, notes preview, contact, footer
About — full version of my story, leading with now and using the arc as backstory
Work — case studies (priority: the AI adoption rollout, an internal product, a Three.js piece)
Notes — a public notebook, organised by topic not date, mixing rough and polished
Etc. — sketches, dog photos, personal stuff, linked from footer only
Contact — probably just a footer surface, not a full page

## Homepage in detail

Hero: name (should visually dominate), positioning line "Technical delivery lead at Jarilo Design. Self-taught, hospitality-raised, still ships code.", current-focus line "Currently shipping 350+ projects a year and figuring out what AI-augmented delivery actually looks like in practice."
Proof strip: horizontal row of the four metrics above, numbers dominant, labels smaller and lighter.
Selected Work: 3–4 items in a list format (not cards). Category tag, title, one-line description. Thumbnails only on visual/technical items, not delivery case studies. Muted category tag colours — they should label, not shout.

## About preview: three short paragraphs, no photo, "More about me →" link. Aligned to the main left grid, not indented.

## Notes preview: "A public notebook — thoughts, hacks and lessons, primarily for myself." 3–4 recent notes with topic tags. Titles should visually dominate the tag and preview line. No dates. "All notes →" link.

Footer: Email / GitHub / LinkedIn as plain text links. "Etc." on the far right. Colophon line: "Built with Astro". Copyright.

## Notes section framing

Not a blog — a public notebook, primarily for me. No dates on entries, organised by topic tags. Comfortable with short rough notes as well as polished pieces. Content buckets I want to write in: delivery/process thinking, AI-augmented workflows (highest priority — the AI adoption rollout is the strongest single piece I could publish), technical-leader-who-still-codes perspective, occasional career/learning reflection.

## Design direction

Editorial, typography-led. Restrained foundation with small deliberate moments of personality. References: Maggie Appleton, Brian Lovin, Rauno Freiberg, Paul Stamatiou. Warm off-white background, muted terracotta accent (subtle nod to an earlier orange-heavy version of my site), dark-not-black text. Serif headings, readable sans body — I'm leaning toward Fraunces for headings (closest free alternative to Canela Deck). Generous whitespace throughout, but with dynamics — one or two elements (hero name, section headings) allowed to be visually confident so restraint elsewhere reads as deliberate.
No: gradient backgrounds, mouse-follow effects, parallax, scroll-jacking, testimonials, services section, skills cloud, newsletter signup, contact form. Light mode primary, dark mode welcome if tasteful.

## Technical

Astro 6
Content collections with MDX for case studies and notes
Hosting: Netlify
React islands only for genuinely interactive things (Three.js, demos), not layout
View transitions if they can be done tastefully
Semantic HTML, accessible contrast and focus states, mobile-considered not just responsive

## Where I'd like to start

Set up the project structure, content collections for Work and Notes, and get a working homepage rendering with placeholder content — then we can iterate on real content and design polish from there.

## Development

When starting the dev server, use background mode:

```
astro dev --background
```

When the dev server loads, console.log the message "Hello Darran"

Manage the background server with `astro dev stop`, `astro dev status`, and `astro dev logs`.

## Documentation

Full documentation: https://docs.astro.build

Consult these guides before working on related tasks:

- [Adding pages, dynamic routes, or middleware](https://docs.astro.build/en/guides/routing/)
- [Working with Astro components](https://docs.astro.build/en/basics/astro-components/)
- [Using React, Vue, Svelte, or other framework components](https://docs.astro.build/en/guides/framework-components/)
- [Adding or managing content](https://docs.astro.build/en/guides/content-collections/)
- [Adding styles or using Tailwind](https://docs.astro.build/en/guides/styling/)
- [Supporting multiple languages](https://docs.astro.build/en/guides/internationalization/)
