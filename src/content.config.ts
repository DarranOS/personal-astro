import { defineCollection } from "astro:content";
import { z } from "astro/zod";
import { glob } from "astro/loaders";

const blog = defineCollection({
  loader: glob({ base: "./src/content/blog", pattern: "**/*.{md,mdx}" }),
  schema: ({ image }) =>
    z.object({
      title: z.string(),
      subtitle: z.string(),
      tags: z.array(z.string()).default([]),
      poster: image().optional(),
    }),
});

export const collections = { blog };
