# Astro Starter Kit: Basics

```sh
npm create astro@latest -- --template basics
```

https://www.reddit.com/r/web_design/comments/1uqnyxc/show_me_some_minimalistic_but_impressive_frontend/

> 🧑‍🚀 **Seasoned astronaut?** Delete this file. Have fun!

## 🚀 Project Structure

Inside of your Astro project, you'll see the following folders and files:

```text
/
├── public/
│   └── favicon.svg
├── src
│   ├── assets
│   │   └── astro.svg
│   ├── components
│   │   └── Welcome.astro
│   ├── layouts
│   │   └── Layout.astro
│   └── pages
│       └── index.astro
└── package.json
```

To learn more about the folder structure of an Astro project, refer to [our guide on project structure](https://docs.astro.build/en/basics/project-structure/).

## 🧞 Commands

All commands are run from the root of the project, from a terminal:

| Command                   | Action                                           |
| :------------------------ | :----------------------------------------------- |
| `npm install`             | Installs dependencies                            |
| `npm run dev`             | Starts local dev server at `localhost:4321`      |
| `npm run build`           | Build your production site to `./dist/`          |
| `npm run preview`         | Preview your build locally, before deploying     |
| `npm run astro ...`       | Run CLI commands like `astro add`, `astro check` |
| `npm run astro -- --help` | Get help using the Astro CLI                     |

## 👀 Want to learn more?

Feel free to check [our documentation](https://docs.astro.build) or jump into our [Discord server](https://astro.build/chat).

Here's both pieces, tailored to a standard Astro project structure like yours.

1. public/robots.txt
   Create this file at public/robots.txt (sits alongside your other static assets, Astro serves it straight from the root):
   User-agent: \*
   Disallow: /
2. Meta tag in your layout
From the earlier VS Code log, you've got src/pages/index.astro and src/components/Welcome.astro — if you've got a shared layout wrapping your pages (commonly src/layouts/Layout.astro), that's the best place to add this once so it applies everywhere.
If you have a layout file, find the <head> section and add:
astro<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width" />
  <meta name="robots" content="noindex, nofollow" />
  <!-- ...your existing head content... -->
</head>
If you don't have a shared layout yet and each page manages its own <head>, just add that same <meta name="robots" content="noindex, nofollow" /> line into the <head> of index.astro directly, and any other page files you create.
A reminder for future-you
Worth dropping a comment right above that meta tag so it doesn't get forgotten when you're ready to launch:
astro<!-- TODO: remove noindex before public launch -->
<meta name="robots" content="noindex, nofollow" />
