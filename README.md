# Site dos Bombeiros Voluntários de Tábua

Site oficial da Associação Humanitária dos Bombeiros Voluntários de Tábua, publicado em
[bvtabua.pt](https://bvtabua.pt).

Feito com [Astro](https://astro.build) (site estático) e publicado automaticamente no alojamento cPanel, por FTP,
a cada alteração no ramo `main`. Até ao lançamento, o site está protegido por palavra-passe (Directory Privacy do
cPanel) e não é indexado pelos motores de busca.

## Páginas

| Endereço | Página |
| --- | --- |
| `/` | Início: 112, botões de apoio, serviços, notícias, risco de incêndio |
| `/associacao/` | História, órgãos sociais, memória, transparência |
| `/corpo-de-bombeiros/` | Comando, efetivo, missões, frota, quartel |
| `/servicos/` | Transporte de doentes, prevenções, formação |
| `/apoiar/` | Ser sócio, donativos (MB Way e IBAN), mecenato |
| `/ser-bombeiro/` | Recrutamento: requisitos, percurso, perguntas, candidatura |
| `/prevencao/` | Risco de incêndio, queimadas, conselhos |
| `/noticias/` | Notícias e eventos |
| `/contactos/` | Contactos, formulário, canal de denúncias |
| `/privacidade/`, `/acessibilidade/` | Informação legal |

## Antes do lançamento

- Substituir os dados provisórios em «Definições gerais» no painel (telefone, morada, IBAN, MB Way, NIF, quota,
  redes sociais).
- Substituir os blocos amarelos «Texto provisório» pelos textos da associação, no painel.
- Colocar logótipo e fotografias em `public/imagens/` e os PDFs de transparência em `public/documentos/`.
- Opcional: criar uma conta gratuita no [Formspree](https://formspree.io) ou [Web3Forms](https://web3forms.com)
  e colocar o endereço em «Definições gerais → Endereço dos formulários» para os formulários enviarem emails. Sem isso,
  os formulários mostram o email e o telefone.

## Painel para editar o site (backend)

Todo o conteúdo do site edita-se no painel do [Pages CMS](https://app.pagescms.org), sem programar:

| Menu do painel | O que edita | Ficheiro |
| --- | --- | --- |
| Notícias | Notícias e eventos, com fotografia | `src/content/noticias/*.md` |
| Definições gerais | Contactos, IBAN, MB Way, NIF, quota, redes sociais, logótipo | `src/content/definicoes.json` |
| Página: … | Textos e listas de cada página (cartões, órgãos sociais, frota, perguntas, documentos) | `src/content/paginas/*.md` |

Cada alteração guardada no painel é gravada neste repositório e o site é republicado automaticamente (ver
«Alojamento»). As fotografias vão para `public/imagens/` e os PDFs para `public/documentos/`.

**Dar acesso a outras pessoas:** no painel, em *Settings → Collaborators*, convide-as pelo email (não precisam de
conta GitHub). Quem for colaborador do repositório no GitHub também pode entrar com a sua conta.

**Nos textos:** `{{telefone}}`, `{{email}}`, `{{morada}}`, `{{nif}}`, `{{iban}}`, `{{mbway}}`, `{{quota}}`,
`{{associacao}}`, `{{nome}}`, `{{horario}}` e `{{emergencia}}` são trocados pelos valores das Definições gerais. Uma
citação que comece por «**Texto provisório.**» aparece como aviso amarelo.

A configuração do painel está em `.pages.yml`. O código que mostra os textos está em `src/lib/texto.ts`.

## Desenvolvimento

```sh
npm install
npm run dev      # http://localhost:4321
npm run build    # gera o site em dist/
```

## Alojamento e domínio

O domínio `bvtabua.pt` aponta para o alojamento cPanel (registo A `62.193.193.44`).

1. Publicação automática: em GitHub → Settings → Secrets and variables → Actions, criar os segredos
   `FTP_SERVER`, `FTP_USERNAME` e `FTP_PASSWORD` com os dados de uma conta FTP do cPanel. Sem eles, o site
   só é compilado e tem de ser enviado à mão (pasta `dist/` para `public_html/`).
2. Proteção por palavra-passe: cPanel → Directory Privacy → `public_html` → ativar a proteção e criar
   utilizadores.
3. No lançamento: retirar a proteção em Directory Privacy e trocar o `public/robots.txt` para permitir a
   indexação.
