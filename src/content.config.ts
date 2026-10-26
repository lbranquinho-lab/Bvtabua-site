import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

const noticias = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/noticias' }),
  schema: z.object({
    titulo: z.string(),
    data: z.coerce.date(),
    resumo: z.string(),
    imagem: z.string().optional(),
    rascunho: z.boolean().default(false),
  }),
});

export const collections = { noticias };
