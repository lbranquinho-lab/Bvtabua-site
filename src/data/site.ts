// Dados da associação usados em todo o site.
// Os valores marcados com "A CONFIRMAR" são provisórios e têm de ser substituídos
// pelos dados reais antes do lançamento.

export const site = {
  nome: 'Bombeiros Voluntários de Tábua',
  nomeCurto: 'BV Tábua',
  associacao: 'Associação Humanitária dos Bombeiros Voluntários de Tábua',
  descricao:
    'Associação Humanitária dos Bombeiros Voluntários de Tábua: socorro, emergência pré-hospitalar e proteção civil no concelho de Tábua desde a sua fundação.',

  emergencia: '112',
  telefone: '235 000 000', // A CONFIRMAR
  email: 'geral@bvtabua.pt', // A CONFIRMAR
  morada: 'Rua dos Bombeiros Voluntários, 3420-000 Tábua', // A CONFIRMAR
  horarioSecretaria: 'Dias úteis, das 9h00 às 12h30 e das 14h00 às 17h30', // A CONFIRMAR
  mapaUrl: 'https://www.openstreetmap.org/search?query=Bombeiros%20Volunt%C3%A1rios%20de%20T%C3%A1bua',

  // Donativos
  iban: 'PT50 0000 0000 0000 0000 0000 0', // A CONFIRMAR
  mbway: '900 000 000', // A CONFIRMAR
  nif: '500 000 000', // A CONFIRMAR
  quotaAnual: '12 €', // A CONFIRMAR

  // Endereço que recebe os formulários (ex.: Formspree ou Web3Forms).
  // Vazio: os formulários mostram o email e o telefone em alternativa.
  formEndpoint: '',

  redes: {
    facebook: 'https://www.facebook.com/', // A CONFIRMAR
    instagram: 'https://www.instagram.com/', // A CONFIRMAR
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
