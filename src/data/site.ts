// Dados da associação usados em todo o site.
// Os valores ficam em src/content/definicoes.json e editam-se no painel
// (Pages CMS → «Definições gerais»). Os provisórios têm de ser substituídos antes do lançamento.
import dados from '../content/definicoes.json';

export const site = {
  ...dados,
  redes: {
    facebook: dados.facebook,
    instagram: dados.instagram,
  },
};

export const navegacao = [
  { href: '/associacao/', label: 'A Associação' },
  { href: '/corpo-de-bombeiros/', label: 'Corpo de Bombeiros' },
  { href: '/servicos/', label: 'Serviços' },
  { href: '/prevencao/', label: 'Prevenção' },
  { href: '/noticias/', label: 'Notícias' },
  { href: '/contactos/', label: 'Contactos' },
];

export const telLink = (numero: string) => `tel:${numero.replace(/\s/g, '')}`;
