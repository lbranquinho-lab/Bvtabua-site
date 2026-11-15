// Converte os textos escritos no painel (Markdown ou HTML) em HTML para as páginas.
//
// - {{telefone}}, {{email}}, {{morada}}, {{nif}}, {{iban}}, {{mbway}}, {{quota}}, {{associacao}}, {{nome}},
//   {{horario}} e {{emergencia}} são substituídos pelos valores das «Definições gerais»;
//   telefone, email e emergência ficam com ligação.
// - Uma citação que comece por «Texto provisório» aparece como aviso amarelo.
import { marked } from 'marked';
import { site, telLink } from '../data/site';

// Os títulos ganham um id (ex.: «A nossa história» → #a-nossa-historia) para se poder ligar a eles.
const slug = (t: string) =>
  t
    .replace(/<[^>]+>/g, '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '');

marked.use({
  renderer: {
    heading({ tokens, depth }) {
      const texto = this.parser.parseInline(tokens);
      return `<h${depth} id="${slug(texto)}">${texto}</h${depth}>\n`;
    },
  },
});

const escapar = (t: string) =>
  t.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

const valores: Record<string, () => string> = {
  telefone: () => `<a href="${telLink(site.telefone)}">${escapar(site.telefone)}</a>`,
  emergencia: () => `<a href="${telLink(site.emergencia)}"><strong>${escapar(site.emergencia)}</strong></a>`,
  email: () => `<a href="mailto:${escapar(site.email)}">${escapar(site.email)}</a>`,
  morada: () => escapar(site.morada),
  nif: () => escapar(site.nif),
  iban: () => escapar(site.iban),
  mbway: () => escapar(site.mbway),
  quota: () => escapar(site.quotaAnual),
  associacao: () => escapar(site.associacao),
  nome: () => escapar(site.nome),
  horario: () => escapar(site.horarioSecretaria),
};

export const substituir = (texto: string) =>
  texto.replace(/\{\{\s*(\w+)\s*\}\}/g, (todo, chave: string) => (valores[chave] ? valores[chave]() : todo));

export function html(texto: string | undefined | null): string {
  if (!texto) return '';
  const resultado = marked.parse(substituir(texto), { async: false }) as string;
  return resultado.replace(
    /<blockquote>\s*<p>\s*(?:<strong>)?\s*Texto provisório\.?:?\s*(?:<\/strong>)?\s*([\s\S]*?)<\/p>\s*<\/blockquote>/gi,
    '<p class="provisorio"><strong>Texto provisório.</strong> $1</p>',
  );
}

// Versão numa linha, sem parágrafo à volta (para títulos e resumos).
export function linha(texto: string | undefined | null): string {
  if (!texto) return '';
  return marked.parseInline(substituir(texto), { async: false }) as string;
}
