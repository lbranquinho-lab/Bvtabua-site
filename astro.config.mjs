// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';

export default defineConfig({
  site: 'https://bvtabua.pt',
  integrations: [sitemap({ filter: (pagina) => !pagina.includes('/area-reservada/') })],
});
