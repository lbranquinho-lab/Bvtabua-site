import { getEntry } from 'astro:content';

// Lê o ficheiro de uma página (src/content/paginas/<nome>.md): campos e texto principal.
export async function pagina(nome: string) {
  const entrada = await getEntry('paginas', nome);
  if (!entrada) throw new Error(`Falta o ficheiro src/content/paginas/${nome}.md`);
  return { ...(entrada.data as Record<string, any>), corpo: entrada.body ?? '' };
}
