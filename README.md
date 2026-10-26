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

- Substituir todos os valores marcados com `A CONFIRMAR` em `src/data/site.ts` (telefone, morada, IBAN,
  MB Way, NIF, quota, redes sociais).
- Substituir os blocos amarelos «Texto provisório» pelos textos da associação.
- Colocar logótipo e fotografias em `public/imagens/` e os PDFs de transparência em `public/documentos/`.
- Opcional: criar uma conta gratuita no [Formspree](https://formspree.io) ou [Web3Forms](https://web3forms.com)
  e colocar o endereço em `formEndpoint` (`src/data/site.ts`) para os formulários enviarem emails. Sem isso,
  os formulários mostram o email e o telefone.

## Publicar notícias sem programar

Entre em [app.pagescms.org](https://app.pagescms.org) com a conta GitHub, escolha este repositório e use o
menu «Notícias». Cada notícia guardada é publicada automaticamente em poucos minutos.

Também pode criar um ficheiro `.md` em `src/content/noticias/` diretamente no GitHub.

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
