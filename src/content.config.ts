import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

const noticias = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/noticias' }),
  schema: z.object({
    titulo: z.string(),
    data: z.coerce.date(),
    resumo: z.string(),
    imagem: z.string().optional().nullable(),
    rascunho: z.boolean().default(false),
  }),
});

// Textos das páginas, editáveis no painel. Cada página tem os seus campos.
const paginas = defineCollection({
  loader: glob({ pattern: '*.md', base: './src/content/paginas' }),
  schema: z
    .object({
      titulo: z.string(),
      resumo: z.string().optional().nullable(),
      descricao: z.string().optional().nullable(),
    })
    .passthrough(),
});

export const collections = { noticias, paginas };
