# Site dos Bombeiros Voluntários de Tábua

Site oficial da Associação Humanitária dos Bombeiros Voluntários de Tábua, publicado em
[bvtabua.pt](https://bvtabua.pt).

Feito com [Astro](https://astro.build) (site estático) e publicado automaticamente no GitHub Pages a cada
alteração no ramo `main`.

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

1. No GitHub: Settings → Pages → Source: **GitHub Actions**.
2. No registo DNS de `bvtabua.pt`, criar quatro registos `A` para `@` com os IPs `185.199.108.153`,
   `185.199.109.153`, `185.199.110.153` e `185.199.111.153`, e um `CNAME` para `www` apontado para
   `<utilizador>.github.io`.
3. Em Settings → Pages, confirmar o domínio `bvtabua.pt` e ativar **Enforce HTTPS**.
