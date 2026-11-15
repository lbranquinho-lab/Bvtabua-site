// Dados da associação usados em todo o site.
// Os valores ficam em src/content/definicoes.json e editam-se no painel
// (Pages CMS → «Definições gerais»). Os provisórios têm de ser substituídos antes do lançamento.
import ficheiro from '../content/definicoes.json';

// O painel apaga do ficheiro os campos que ficam vazios. Todos os campos têm aqui um valor por omissão
// (texto vazio), para o site nunca falhar a compilar por faltar um campo.
const campos = [
  'nome', 'nomeCurto', 'associacao', 'descricao', 'logotipo', 'emergencia', 'telefone', 'email', 'morada',
  'horarioSecretaria', 'mapaUrl', 'iban', 'mbway', 'nif', 'quotaAnual', 'formEndpoint', 'facebook', 'instagram',
] as const;

const lidos = ficheiro as Partial<Record<(typeof campos)[number], unknown>>;
const dados = Object.fromEntries(
  campos.map((c) => [c, lidos[c] == null ? '' : String(lidos[c]).trim()]),
) as Record<(typeof campos)[number], string>;

if (!dados.nome) dados.nome = 'Bombeiros Voluntários de Tábua';
if (!dados.associacao) dados.associacao = dados.nome;
if (!dados.emergencia) dados.emergencia = '112';

export const site = {
  ...dados,
  // Caminhos de imagens com espaços (ex.: «/imagens/LOGO BVT.png») passam a endereços válidos.
  logotipo: dados.logotipo ? encodeURI(dados.logotipo) : '',
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

export const telLink = (numero: string | undefined) => `tel:${(numero ?? '').replace(/\s/g, '')}`;
