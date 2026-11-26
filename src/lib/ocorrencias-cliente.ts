// Código partilhado (no browser) para mostrar as ocorrências lidas de /api/ocorrencias.php.

export type Ocorrencia = {
  tabua: boolean;
  distrito: string;
  concelho: string;
  natureza: string;
  estado: string;
  cor: string;
  freguesia: string;
  localidade: string;
  hora: string;
  data: string;
  inicio: number;
  operacionais: number;
  terrestres: number;
  aereos: number;
  incendio: boolean;
  importante: boolean;
  lat?: number;
  lng?: number;
};

export type Resposta = { ativo: boolean; atualizado?: number; ocorrencias?: Ocorrencia[] };

export async function lerOcorrencias(): Promise<Ocorrencia[] | null> {
  try {
    const r = await fetch('/api/ocorrencias.php', { cache: 'no-store' });
    if (!r.ok) return null;
    const dados: Resposta = await r.json();
    if (!dados?.ativo || !Array.isArray(dados.ocorrencias)) return null;
    return dados.ocorrencias;
  } catch {
    return null;
  }
}

const plural = (n: number, um: string, varios: string) => `${n} ${n === 1 ? um : varios}`;

function texto(etiqueta: string, conteudo: string, classe?: string) {
  const el = document.createElement(etiqueta);
  el.textContent = conteudo;
  if (classe) el.className = classe;
  return el;
}

export function cartao(o: Ocorrencia, mostrarConcelho = false) {
  const li = document.createElement('li');
  li.className = 'ocorrencia';
  if (o.cor) li.style.setProperty('--cor', `#${o.cor}`);
  li.append(texto('strong', o.natureza || 'Ocorrência'));
  li.append(texto('span', o.estado, 'estado'));
  const onde = [o.localidade, o.freguesia, mostrarConcelho ? o.concelho : '', mostrarConcelho ? o.distrito : '']
    .filter(Boolean)
    .filter((v, i, a) => a.indexOf(v) === i)
    .join(', ');
  const quando = o.hora ? `desde as ${o.hora}` : '';
  if (onde || quando) li.append(texto('p', [onde, quando].filter(Boolean).join(' · '), 'onde'));
  const meios = [
    o.operacionais ? plural(o.operacionais, 'operacional', 'operacionais') : '',
    o.terrestres ? plural(o.terrestres, 'meio terrestre', 'meios terrestres') : '',
    o.aereos ? plural(o.aereos, 'meio aéreo', 'meios aéreos') : '',
  ].filter(Boolean);
  if (meios.length) li.append(texto('p', meios.join(' · '), 'meios'));
  return li;
}

export const horaAgora = () =>
  `Atualizado às ${new Date().toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' })}`;

export const resumo = (lista: Ocorrencia[]) => {
  const incendios = lista.filter((o) => o.incendio).length;
  const operacionais = lista.reduce((t, o) => t + (o.operacionais || 0), 0);
  return { total: lista.length, incendios, operacionais };
};

export { plural };
