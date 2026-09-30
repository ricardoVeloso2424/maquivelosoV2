# MaquiVeloso — Auditoria Completa

> Documento estruturado para consumo por IA. Cada achado tem ID estável, prioridade, confiança e tarefa do plano de ação. O PDF com o mesmo nome contém as mesmas conclusões em formato de leitura.

---

## 1. Metadata

```yaml
projeto: MaquiVeloso (catálogo de máquinas de costura + backoffice)
repositorio: github.com/ricardoVeloso2424/maquivelosoV2
commit_auditado: 86bad95 (merge do PR #3, 2026-09-30 11:02 +0100)
branch: main (igual a origin/main; working tree limpo)
data_auditoria: 2026-09-30
auditor: Claude Code (Claude Opus 5.5), revisão externa
ambito: funcional, regras de preço, segurança, UI/UX responsiva, acessibilidade, performance,
        arquitetura, base de dados, testes, dependências, SEO, privacidade, deploy readiness
alteracoes_ao_projeto: nenhuma (só foram criados os dois ficheiros em docs/audit/)
ambiente_de_teste:
  copia: clone isolado do commit 86bad95 fora do projeto (scratchpad), instalado com composer install e npm ci a partir dos lockfiles
  base_de_dados: SQLite (clone); MariaDB não testada nesta ronda (Docker indisponível)
  servidor: php -S 127.0.0.1:8765, memory_limit=128M, upload_max_filesize=20M, post_max_size=100M
  dados: 20 máquinas criadas pelo formulário real do backoffice (25 imagens 4032x3024 ~4,2 MB, 1 foto com EXIF Orientation=6, 1 foto com GPS, 1 de 48 MP)
stack: Laravel 12.48.1, PHP 8.4.23 (projeto exige ^8.2), Blade + Alpine + Tailwind 3.4.19, Vite 7.3.1, Breeze, Livewire 4.0.2 (instalado, sem uso)
ferramentas: Playwright 1.63.0 + Chrome 154, axe-core 4.13.0, composer 2.10.2 (audit, validate), npm 11.19.0 (audit), php artisan test
viewports: [375, 390, 768, 1024, 1440]
legenda_confianca:
  Confirmado: reproduzido ou medido nesta auditoria
  Provável: dedução direta do código/configuração, não reproduzida ponta a ponta
  Não confirmado: hipótese ou verificação não possível nesta ronda
legenda_prioridade:
  P0: bloqueia o deploy
  P1: corrigir antes do lançamento público
  P2: corrigir nas primeiras semanas
  P3: melhoria / manutenção
nota_ambiente_local: no projeto local (não no clone) a migration 2026_06_14_120000_add_thumb_path_to_machine_images_table está Pending; correr `php artisan migrate` localmente antes de testar as miniaturas.
```

---

## 2. Executive State

**Veredicto:** sem bloqueadores técnicos (0 × P0). O deploy é tecnicamente possível, mas o lançamento público deve esperar pelos 6 × P1 (≈3 dias de trabalho) e por três decisões do dono: fornecedor de email, limites do alojamento e validação jurídica das páginas legais.

**Contagens:** P0 = 0 · P1 = 6 · P2 = 17 · P3 = 26 · total = 49.

**Maior risco:** as fotos tiradas na vertical com o telemóvel aparecem de lado no catálogo, na página inicial e no backoffice (FUNC-001). Acresce que, com os limites por omissão do PHP e do Nginx documentado, o próprio upload dessas fotos falha (DEPLOY-001). O fluxo principal do negócio (fotografar a máquina e publicá-la) fica afetado pelos dois lados.

**Resolvido desde a auditoria anterior (41f699d):**
- Credenciais de administrador por omissão: existe `admin:create` e os seeders não correm em produção (testado em `ProductionSafetyTest`).
- Registo público desligado (GET/POST /register → 404).
- Existem `DEPLOYMENT.md` e `.env.production.example` com HTTPS, permissões, backups e rollback.
- Miniaturas GD: o catálogo passou de ≈46 MB de imagens para ≈2,6 MB por página.
- Regras de preço centralizadas (`Machine::priceState()` + componente `x-machine.price`); preço aceita vírgula (`PriceNormalizer`).
- Escolha da imagem principal (transação e bindings com scope → 404 entre máquinas).
- Mensagens flash no backoffice, identidade visual, menu mobile, skip link, reduced-motion, labels no catálogo, galeria com lightbox, breadcrumb, página de contacto honesta.
- Código morto, aliases antigos e axios removidos; `.gitignore` cobre sqlite e `.claude`.

**Estado geral por área:**

| Área | Estado | Observação |
|---|---|---|
| Funcionalidade | Atenção | Fluxos completos. Miniaturas rodadas (FUNC-001), recuperação de password inoperante em produção (FUNC-002) |
| Regras de preço | Atenção | Os 4 casos estão corretos. Valor arredondado às unidades e "0 €" (FUNC-006) |
| Segurança | Atenção | Controlos base corretos. Dependências com advisories high (SEC-001), sem cabeçalhos de segurança (SEC-002) |
| UI pública | Bom | Sem overflow de 375 a 1440 px, CLS 0. Conteúdo invisível sem JS (UI-002) |
| UI backoffice | Precisa de correção | Scroll horizontal em todas as páginas a 375/390 px; na lista de máquinas também a 768 e 1024 px (UI-001) |
| Acessibilidade | Atenção | Campos sem label no backoffice, foco da lightbox, contraste do CTA WhatsApp |
| Performance | Precisa de correção | Logótipo de 1,17 MB em todas as páginas (PERF-001); detalhe ≈6,8 MB (PERF-002) |
| Arquitetura/código | Bom | Serviço e config centralizados. Restos do skeleton, BOM, JS inline |
| Base de dados | Bom | Índices e FKs adequados. MariaDB por reconfirmar nesta versão |
| Testes | Bom | 98 passam. Não cobrem os defeitos encontrados |
| Dependências | Precisa de correção | 12 advisories high em produção, resolúveis com `composer update` |
| SEO | Atenção | Título igual em todas as páginas, sem OG, sem sitemap |
| Privacidade/legal | Atenção | GPS nos originais públicos; faltam páginas legais (Requer validação jurídica) |
| Deploy | Atenção | Boa documentação; email e limites de upload por definir |

---

## 3. Metrics

```yaml
testes:
  com_build_vite: {passed: 98, skipped: 1, assertions: 273, duracao_s: 2.08}
  sem_build_vite: {failed: 27, skipped: 1, passed: 71, assertions: 224, duracao_s: 8.21, causa: ViteManifestNotFoundException}
build_vite:
  css: {bytes: 67.79 kB, gzip: 11.51 kB}
  js: {bytes: 45.82 kB, gzip: 16.53 kB}
composer:
  validate_strict: válido
  audit_total: {advisories: 45, pacotes: 16, high: 13, medium: 25, low: 6, sem_severidade: 1}
  audit_no_dev: {advisories: 41, pacotes: 14, high: 12, medium: 25, low: 3, sem_severidade: 1}
  update_dry_run: 73 atualizações dentro das constraints atuais (laravel/framework 12.48.1 -> 12.69.3)
npm:
  audit_total: {total: 10, critical: 2, high: 6, moderate: 1, low: 1}
  audit_omit_dev: 0
laravel:
  rotas: 48 (inclui 4 da Livewire, storage.local e /up)
  migrations: 10 (todas aplicadas no clone)
  view_cache: OK
queries_sql_por_pagina: {home: 14, catalogo: 10, catalogo_pagina_2: 10, detalhe: 7, contacto: 4, n_mais_1: não detetado}
peso_primeira_visita_sem_compressao:   # medido no Chrome contra o servidor de teste (sem gzip nem cache)
  home: 2.51 MB
  catalogo: 2.58 MB (375 px) / 2.67 MB (1440 px)
  detalhe_com_5_fotos: 6.76 MB (imagens 5.45 MB)
  detalhe_sem_foto: 2.47 MB
  contacto: 2.47 MB (dos quais 2 x 1.17 MB = logótipo como imagem + como favicon)
  login: 2.46 MB
  404: 8 KB
cls: 0 em todas as páginas e larguras
pedidos_externos: 0 no site público; 1 (fonts.bunny.net) em login e perfil
overflow_horizontal_scrollWidth:
  publico: nenhum em 375/390/768/1024/1440
  admin_375: {dashboard: 419, maquinas: 1156, criar: 426, editar: 426, categorias: 554, definicoes: 409, show: 543}
  admin_768_e_1024: {maquinas: 1188, restantes: sem overflow}
  admin_1440: sem overflow
axe_violacoes_por_pagina_1440:
  home: [color-contrast x2, heading-order x1]
  catalogo: [aria-prohibited-attr x1, color-contrast x4, heading-order x1]
  detalhe: [color-contrast x3]
  contacto: [color-contrast x3, definition-list x1, dlitem x10]
  login: [landmark-one-main, page-has-heading-one, region x5]
  admin_maquinas: [select-name x2 (critical), color-contrast x3, aria-prohibited-attr x1]
  admin_criar_editar: [select-name x2 (critical), color-contrast x3]
  admin_categorias: [label x5 (critical), color-contrast x4]
  perfil_375: [button-name x1 (critical), page-has-heading-one]
alvos_de_toque_menores_que_24px: 3 a 12 por página pública
imagens_upload:
  pico_memoria_gd: {base: 22 MB, 24_MP: 120 MB, 48_MP: 214 MB}
  limite_testado: 128M -> 48 MP falha com HTTP 500
```

---

## 4. Findings

Formato de cada achado: prioridade · confiança · bloqueia deploy · esforço · tarefa, seguido de estado atual, problema, impacto, evidência, ficheiros e recomendação.

### 4.1 P0

Nenhum achado P0 confirmado.

### 4.2 P1

#### SEC-001 — Dependências PHP de produção com advisories publicados
- prioridade: P1
- confiança: Confirmado (advisories); Provável risco de exploração baixo
- bloqueia_deploy: Não (corrigir antes do lançamento)
- esforço: 1–2 h
- tarefa: T01
- estado_atual: o `composer.lock` fixa laravel/framework v12.48.1, symfony/mime v7.4.0, league/commonmark 2.8.0, guzzlehttp/guzzle 7.10.0 e livewire/livewire v4.0.2.
- problema: `composer audit --no-dev` reporta 41 advisories em 14 pacotes (12 high). Os high são:
  - laravel/framework: "CRLF injection in default email rule" (PKSA-3r5d-mb8f-1qw9, versões < 12.60.0).
  - symfony/mime: CVE-2026-45067, injeção de cabeçalhos de email/SMTP via CRLF.
  - guzzlehttp/guzzle: CVE-2026-69246.
  - symfony/http-kernel: CVE-2026-45075.
  - league/commonmark: 8 advisories (DoS e bypass de filtro XSS).
- impacto: nesta app a exploração é limitada. Não há Markdown renderizado nem pedidos HTTP de saída. A regra `email` é usada em login, perfil, definições e recuperação de password, e o único email enviado é o de reposição. Mesmo assim, não se justifica lançar com o framework 21 versões patch atrás e advisories high públicos, quando a correção é trivial.
- evidência: `composer audit --no-dev --format=json`. O `composer update --dry-run` no clone resolve para laravel/framework 12.69.3, symfony/mime 7.4.19, league/commonmark 2.10.3, guzzle 7.15.5 e livewire 4.4.7, sem mexer nas constraints.
- ficheiros: `composer.lock`; `composer.json:12`
- recomendação: correr `composer update` num branch, depois `npm run build && php artisan test`, e fazer commit do lock. Acrescentar `composer audit --no-dev` ao `.github/workflows/tests.yml`.

#### FUNC-001 — Miniaturas ignoram a orientação EXIF (fotos verticais aparecem de lado)
- prioridade: P1
- confiança: Confirmado
- bloqueia_deploy: Não (corrigir antes do lançamento)
- esforço: 2–3 h
- tarefa: T02
- estado_atual: `ThumbnailService::generate()` usa `imagecreatefromjpeg` e `imagecopyresampled`. O GD não aplica a tag Orientation e a miniatura JPEG é gravada sem EXIF.
- problema: os telemóveis gravam as fotos verticais com píxeis em paisagem e Orientation=6 ou 8. O browser roda o original, mas a miniatura fica deitada.
- impacto: as fotos ficam rodadas 90° nos cartões do catálogo, nos destaques da página inicial, na lista e no formulário do backoffice. Afeta o fluxo de upload mais provável (fotos de telemóvel). O dono vê a foto correta no detalhe e rodada na lista, sem perceber porquê.
- evidência: a fixture `rotated.jpg` (2400×1800, Orientation=6) foi enviada pelo formulário real (máquina 8, "Pfaff Hobbylock 2.0"). A miniatura gerada tem 600×450, sem EXIF. O cartão do catálogo mostra a seta a apontar para a esquerda; o detalhe mostra-a a apontar para cima (PDF, figura 1).
- ficheiros: `app/Services/ThumbnailService.php:43-74`; `app/Http/Controllers/Admin/MachineController.php:137-146`
- recomendação:
  - Em JPEG, ler `@exif_read_data($fullPath)['Orientation']` e aplicar `imagerotate`/`imageflip` (casos 2–8) antes de redimensionar, trocando largura e altura nos casos 5–8.
  - Juntar `exif` às extensões exigidas em `DEPLOYMENT.md`.
  - Criar um teste com fixture Orientation=6.
  - Depois do deploy, correr `php artisan machines:generate-thumbnails --force`.

#### FUNC-002 — Recuperação de password não entrega email com a configuração de produção documentada
- prioridade: P1
- confiança: Confirmado
- bloqueia_deploy: Precisa de confirmação (decisão: SMTP sim/não)
- esforço: 3–4 h, mais a escolha do fornecedor SMTP
- tarefa: T03
- estado_atual:
  - `.env.production.example:64` define `MAIL_MAILER=log` e `:25` define `LOG_LEVEL=warning`.
  - `DEPLOYMENT.md:49-52` e `:296` afirmam que a app não envia email.
  - `admin:create` não atua sobre um admin existente ("já é administrador. Nada a fazer.").
- problema: /forgot-password continua ativo e responde "We have emailed your password reset link.". Com o mailer `log`, o email é escrito no log ao nível debug, que `LOG_LEVEL=warning` descarta. Nada é enviado nem guardado.
- impacto:
  - Se o único administrador esquecer a password, só consegue voltar a entrar com acesso SSH e tinker/SQL.
  - A mensagem de sucesso engana.
  - Em local, os links de reposição ficam no `laravel.log` (4 ocorrências no clone). É aceitável em dev, mas mostra que o fluxo depende do log.
- evidência: pedido real ao clone com a mensagem de sucesso. O `LogTransport` do framework regista ao nível `debug` (`vendor/laravel/framework/src/Illuminate/Mail/Transport/LogTransport.php:54`). `CreateAdminUser.php:49-53`.
- ficheiros: `.env.production.example:25,64`; `DEPLOYMENT.md:49-52,296`; `app/Console/Commands/CreateAdminUser.php:49-53`; `routes/auth.php:23-27`
- recomendação:
  - (a) Configurar SMTP transacional em produção, documentá-lo em `DEPLOYMENT.md` e em `.env.production.example`, e corrigir a frase "no outbound email".
  - (b) Criar `php artisan admin:reset-password {email}`, ou uma opção `--reset-password` em `admin:create`.
  - (c) Enquanto não houver SMTP, esconder o link "Forgot your password?".

#### DEPLOY-001 — Limites de upload do servidor não documentados e incompatíveis com o formulário
- prioridade: P1
- confiança: Confirmado (configuração); Provável (413 não reproduzido com Nginx real)
- bloqueia_deploy: Precisa de confirmação (valores do alojamento)
- esforço: 1–2 h
- tarefa: T04
- estado_atual: o backoffice aceita até 8 imagens de 5 MB por pedido (`images max:8`, `images.* max:5120`, até 40 MB). `DEPLOYMENT.md:232` define `client_max_body_size 8M` e o documento não menciona `upload_max_filesize`, `post_max_size` nem `memory_limit`.
- problema:
  - Com os valores por omissão do PHP (upload_max_filesize=2M, post_max_size=8M), uma foto típica de telemóvel (3–5 MB) é rejeitada.
  - Com o Nginx documentado, duas fotos de 4,2 MB (8,4 MB) já dão 413 antes de o Laravel responder.
- impacto: o dono não consegue carregar várias fotos de uma vez. O 413 aparece como página de erro genérica do Nginx, sem mensagem da app.
- evidência: `DEPLOYMENT.md:232`; o grep não encontra `upload_max_filesize`, `post_max_size` nem `memory_limit`; as fotos de teste 4032×3024 têm ≈4,2 MB cada.
- ficheiros: `DEPLOYMENT.md:232` e §2/§7; `app/Http/Controllers/Admin/MachineController.php:175-177`
- recomendação:
  - Documentar e configurar `upload_max_filesize=6M`, `post_max_size=48M`, `max_file_uploads=20`, `memory_limit=256M` (ver FUNC-003) e `client_max_body_size 48M`. Em alternativa, baixar o limite por pedido para caber no alojamento.
  - Mostrar na UI o limite total por envio.

#### UI-001 — Backoffice sem layout responsivo
- prioridade: P1
- confiança: Confirmado
- bloqueia_deploy: Não
- esforço: 1–1,5 dias
- tarefa: T05
- estado_atual: `layouts/admin.blade.php:17` tem a barra lateral `w-72` (288 px) sempre visível e `:103` tem `<main class="relative z-10 flex-1">` sem `min-w-0`. A tabela da lista de máquinas não se adapta (precisa de ≈900 px além da barra lateral).
- problema: a 375 px sobram 87 px para o conteúdo e todas as páginas do backoffice ganham scroll horizontal (o perfil Breeze é a exceção). A lista de máquinas precisa de 1188 px e transborda também a 768 e 1024 px. A 1440 px não há problemas.
- impacto: gerir pelo telemóvel ou tablet é impraticável, e é o cenário natural para fotografar e publicar uma máquina.
- evidência: scrollWidth medido:

  | Página | Largura | scrollWidth |
  |---|---|---|
  | Dashboard | 375 | 419 px |
  | Lista de máquinas | 375 / 390 | 1156 px |
  | Lista de máquinas | 768 / 1024 | 1188 px |
  | Categorias | 375 | 554 px |
  | Criar / editar | 375 | 426 px |
  | Definições | 375 | 409 px |
  | Show | 375 | 543 px |

  Ver PDF, figura 2.
- ficheiros: `resources/views/layouts/admin.blade.php:17,103`; `resources/views/admin/machines/index.blade.php`
- recomendação:
  - Barra lateral off-canvas abaixo de `lg`, com Alpine (já carregado) e botão de menu.
  - `min-w-0` no `main`.
  - Lista de máquinas em cartões abaixo de `md`, ou `overflow-x-auto` no contentor da tabela.
  - Validar a 375, 768 e 1024 px.

#### PERF-001 — Logótipo de 1,17 MB carregado em todas as páginas
- prioridade: P1
- confiança: Confirmado
- bloqueia_deploy: Não
- esforço: 1 h
- tarefa: T06
- estado_atual: `public/images/branding/maquivelosoLogo.png` tem 1254×1254 px e 1 167 785 bytes. É mostrado a 40×40 px no cabeçalho e no rodapé e usado como favicon.
- problema: o ficheiro é cerca de 100× maior do que o necessário.
- impacto: cada página pública transfere ≈2,5 MB na primeira visita, dos quais ≈2,3 MB são o logótipo. O Chrome descarregou-o duas vezes: como imagem e como favicon. O servidor de teste não envia cabeçalhos de cache; em produção com Nginx a segunda transferência pode vir da cache (Não confirmado). Uma página sem fotos pesa tanto como o catálogo com 12 miniaturas.
- evidência: lista de pedidos de /contacto (2 473 391 bytes no total, com duas linhas de 1 167 785 bytes); `metrics.json` (imgKB 1140 em páginas sem fotos).
- ficheiros: `resources/views/layouts/site.blade.php:8,63,128`; `public/images/branding/maquivelosoLogo.png`
- recomendação:
  - Exportar o logótipo em 80×80 e 160×160 (@2x), WebP ou PNG otimizado, abaixo de 15 KB, e usar `srcset`.
  - Criar `favicon.ico` 32×32 e `apple-touch-icon` 180×180 (ver SEO-003).
  - Configurar cache longa para `/images` e `/build` no Nginx.

### 4.3 P2

#### SEC-002 — Cabeçalhos de segurança ausentes e versão do PHP exposta
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 2–3 h · tarefa: T08
- estado_atual: `bootstrap/app.php:14-17` só regista o alias `admin`; não há middleware de cabeçalhos. O exemplo Nginx em `DEPLOYMENT.md` §7 não tem `add_header`.
- problema: as respostas não trazem HSTS, X-Frame-Options/`frame-ancestors`, X-Content-Type-Options, Referrer-Policy, Permissions-Policy nem CSP. No ambiente de teste é enviado `X-Powered-By: PHP/8.4.23`; em produção isso depende de `expose_php`.
- impacto:
  - O backoffice pode ser embebido num iframe (clickjacking de ações como mudar estado ou apagar).
  - Sem HSTS, o primeiro acesso pode ser feito em HTTP.
  - A versão do PHP fica divulgada.
- evidência: `curl -I /` no clone.
- ficheiros: `bootstrap/app.php:14-17`; `DEPLOYMENT.md` §7 (bloco Nginx, perto da linha 232)
- recomendação:
  - No Nginx, ou num middleware:
    - `Strict-Transport-Security: max-age=31536000; includeSubDomains`
    - `X-Frame-Options: DENY`
    - `X-Content-Type-Options: nosniff`
    - `Referrer-Policy: strict-origin-when-cross-origin`
    - `Permissions-Policy: camera=(), microphone=(), geolocation=()`
  - `expose_php=Off`.
  - CSP primeiro em modo report-only (depende de CODE-005).

#### SEC-003 — Recuperação de password permite enumerar emails e não tem limite por IP
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T09
- estado_atual: `routes/auth.php:26-27` não aplica `throttle` ao POST de forgot-password, e a resposta distingue emails existentes de inexistentes.
- problema: um email inexistente recebe "We can't find a user with that email address."; o do admin recebe "We have emailed your password reset link.". Oito pedidos seguidos foram todos aceites (302). O broker só limita reenvios para o mesmo email (60 s).
- impacto: revela o email do administrador (o único utilizador) e facilita ataques dirigidos ao login. O login tem throttle de 5 tentativas (Breeze).
- evidência: pedidos reais ao clone com as duas mensagens.
- ficheiros: `routes/auth.php:26-27`
- recomendação: aplicar `->middleware('throttle:6,1')` ao POST e usar uma mensagem neutra única ("Se o email estiver registado, receberá um link."), em português.

#### SEC-004 — Fotos originais mantêm EXIF (incluindo GPS) e são públicas
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: ≈2 h (dentro de T10) · tarefa: T10
- estado_atual: o upload guarda o ficheiro tal como chega (`MachineController.php:140`). O original é servido no detalhe e na lightbox. A miniatura GD já não tem EXIF (mitigação parcial).
- problema: coordenadas GPS, modelo do telemóvel e data ficam públicos no original.
- impacto: fotos tiradas em casa ou na oficina revelam a localização exata. Requer validação jurídica (RGPD) caso apareçam dados de terceiros.
- evidência: `gps.jpg` com GPS 41°33'N 8°25'W foi carregado; o ficheiro em `storage` tem o mesmo sha1 do enviado e é servido pelo URL público.
- ficheiros: `app/Http/Controllers/Admin/MachineController.php:140`; `resources/views/site/machine-show.blade.php:52-61,176`
- recomendação: depois de aplicar a orientação (FUNC-001), recodificar o original ao guardar sem EXIF, ou gerar um derivado "large" sem EXIF e deixar de servir o original (ver PERF-002). Reprocessar as fotos existentes.

#### SEC-005 — Livewire instalado sem uso, com rotas e script expostos
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,5 h · tarefa: T01
- estado_atual: `composer.json:12` inclui `livewire/livewire ^4.0`, mas não há nenhum componente. O `route:list` mostra as rotas da Livewire (update, upload-file, preview-file, livewire.js). O `livewire.js` (514 797 bytes) é servido publicamente, e a v4.0.2 tem um advisory medium.
- impacto: superfície de ataque e de manutenção sem qualquer benefício.
- ficheiros: `composer.json:12`
- recomendação: `composer remove livewire/livewire`, correr os testes e fazer commit do lock.

#### FUNC-003 — Imagens de alta resolução esgotam a memória e deixam estado parcial
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 3–4 h · tarefa: T10
- estado_atual: o `ThumbnailService` descodifica a imagem inteira sem verificar o número de píxeis. O `store()` cria a máquina e depois as imagens, sem transação nem limpeza em caso de erro.
- problema: com memory_limit 128M, um JPEG de 8000×6000 (48 MP, 0,79 MB, dentro do limite de 5 MB) provoca "Allowed memory size of 134217728 bytes exhausted". É um erro fatal, que não se consegue capturar.
- impacto:
  - O dono vê HTTP 500 com a página vazia.
  - A máquina fica criada sem imagem e o original fica órfão em `storage/app/public/machines`.
  - É provável que o dono repita o envio e crie duplicados.
  - Picos de memória: 22 MB de base, 120 MB com 24 MP (perto do limite), 214 MB com 48 MP.
- evidência: upload real no clone (máquina sem imagem, ficheiro órfão `2XuliQHIYc7Y8d395OG21g7lxP8LyD17aB2I79m9.jpg`); erro reproduzido via tinker.
- ficheiros: `app/Services/ThumbnailService.php:43-74`; `app/Http/Controllers/Admin/MachineController.php:62-71,126-150,100-111`
- recomendação:
  - Validar `dimensions:max_width=8000,max_height=8000` no pedido.
  - No serviço, não gerar a miniatura quando `w*h*5 > memória livre` (via `ini_get('memory_limit')`).
  - Pôr `memory_limit=256M` no servidor (DEPLOY-001).
  - Criar a máquina e as imagens numa transação e apagar os ficheiros já gravados se houver exceção.

#### FUNC-004 — Marca e modelo sem campos no backoffice; pesquisa pública só por nome
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 2–3 h · tarefa: T11
- estado_atual: as colunas `brand` e `model` existem; a validação aceita-as (`MachineController.php:168-169`) e a pesquisa do backoffice usa-as (`:36-38`). Mas `_form.blade.php` não tem esses campos e a pesquisa pública usa só `name` (`Site/CatalogController.php:53`).
- impacto: não é possível preencher a marca, o atributo que o cliente mais procura (Singer, Brother…). Pesquisar "Brother" só funciona se a marca estiver no nome.
- ficheiros: `resources/views/admin/machines/_form.blade.php`; `app/Http/Controllers/Site/CatalogController.php:53`; `resources/views/site/machine-show.blade.php` (especificações)
- recomendação: adicionar os campos marca e modelo ao formulário, mostrá-los nas especificações do detalhe e pesquisar em name/brand/model no site. Filtro por marca opcional.

#### FUNC-005 — Apagar máquina só existe numa página a que nenhum link leva
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T11
- estado_atual: o botão Apagar só existe em `admin/machines/show.blade.php:23`. A lista só tem link para editar (`index.blade.php:190`) e nenhuma página liga à show.
- impacto: para apagar, o dono tem de escrever o URL `/admin/machines/{id}`. Na prática usa "inativo" e acumula registos e ficheiros.
- ficheiros: `resources/views/admin/machines/index.blade.php:190`; `resources/views/admin/machines/_form.blade.php`; `resources/views/admin/machines/show.blade.php:23`
- recomendação: ação "Apagar" com confirmação na lista e no formulário de edição, ou ligar a página show.

#### FUNC-006 — Preço arredondado às unidades e preço 0 mostrado como "0 €"
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T12
- estado_atual: `Machine.php:75` usa `number_format((float) $this->price, 0, ',', '.') . ' €'`.
- problema: o preço 1234,56 aparece como "1.235 €" no cartão e no detalhe (máquina 6). O preço 0 aparece como "0 €" (máquina Janome).
- impacto: o preço mostrado difere do registado (+0,44 €). A indicação de preços ao consumidor tem de corresponder ao preço praticado, incluindo a menção a IVA: Requer validação jurídica. "0 €" sugere uma oferta.
- ficheiros: `app/Models/Machine.php:69-76`
- recomendação: mostrar 2 casas decimais quando houver cêntimos e nenhuma quando o valor é inteiro. Tratar `price <= 0` como "Sob consulta", ou validar `gt:0`. Criar teste de view (TEST-002).

#### FUNC-007 — Número de WhatsApp sem validação de formato internacional
- prioridade: P2 · confiança: Confirmado (código); Provável (comportamento do wa.me sem indicativo) · bloqueia_deploy: Não · esforço: 1–2 h · tarefa: T13
- estado_atual: `SettingsController.php:39` só valida `nullable|string|max:50`. O componente `whatsapp-button.blade.php:8-11` tira os não-dígitos e gera `wa.me/{dígitos}`. A página de contacto mostra os dígitos crus ("351912345678").
- problema: se o dono escrever "912 345 678" sem o 351, todos os botões "Contactar no WhatsApp" (a conversão principal) apontam para um número inválido, sem aviso.
- ficheiros: `app/Http/Controllers/Admin/SettingsController.php:39`; `resources/views/components/whatsapp-button.blade.php:8-11`; `resources/views/site/contact.blade.php`
- recomendação:
  - Depois de limpar o número, validar `^\d{11,15}$` e exigir indicativo, ou prefixar 351 quando tiver 9 dígitos começados por 9.
  - Mostrar o número formatado ("+351 912 345 678").
  - Pré-visualizar o link no backoffice.

#### FUNC-008 — Informação legal obrigatória em falta (Requer validação jurídica)
- prioridade: P2 · confiança: Provável · bloqueia_deploy: Precisa de confirmação (jurídica) · esforço: 0,5 dia técnico, mais validação · tarefa: T14
- estado_atual: não existe política de privacidade, termos, link para o Livro de Reclamações Eletrónico, informação sobre entidades de Resolução Alternativa de Litígios (RAL), nem denominação social ou NIF. O rodapé só tem contactos.
- impacto: risco de incumprimento para um site comercial dirigido a consumidores em Portugal: DL 156/2005, alterado pelo DL 74/2017 (Livro de Reclamações Eletrónico); Lei 144/2015 (RAL); RGPD art. 13.º. Requer validação jurídica.
- ficheiros: `resources/views/layouts/site.blade.php` (rodapé); `routes/web.php`
- recomendação: criar as páginas /privacidade e /informacao-legal e pôr no rodapé links para livroreclamacoes.pt e para a entidade RAL, mais a identificação da empresa. Os textos devem ser validados por um jurista.

#### UI-002 — Conteúdo principal invisível sem JavaScript
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T15
- estado_atual: `resources/css/app.css:61-65` aplica `opacity:0` a `[data-reveal]` sempre; só o JS (`resources/js/app.js:14-40`) adiciona `.is-visible`. O comentário em `app.js:11-12` fala de progressive enhancement, mas o CSS não degrada.
- evidência:
  - Com JavaScript desativado ficam invisíveis os 11 blocos da página inicial (incluindo os destaques), os 12 cartões do catálogo e os 2 blocos de contactos (PDF, figura 3).
  - Com JS e scroll real aparecem todos (verificado com a roda do rato).
  - Na impressão da página inicial sem scroll ficam 8 blocos em branco.
- impacto: se o bundle JS falhar (manifest errado num deploy, bloqueador, rede móvel instável, browser antigo), o catálogo aparece vazio, embora diga "17 resultado(s)".
- ficheiros: `resources/css/app.css:61-70`; `resources/js/app.js:14-40`; `resources/views/layouts/site.blade.php:2`
- recomendação: esconder só quando há JS. Pôr `class="no-js"` no `<html>`, com um script inline no `<head>` a trocá-la por `js`, e usar o seletor `.js [data-reveal]`. Alternativa: `@media (scripting: enabled)`. Manter o reduced-motion.

#### UI-003 — Interface parcialmente em inglês e páginas de erro por omissão
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,5 dia · tarefa: T16
- estado_atual: `.env.production.example:17` e `config/app.php:81` usam `APP_LOCALE=en` e não há ficheiros `lang/pt`. `layouts/admin.blade.php:3` usa o locale, o que dá `lang="en"`. Não existem views em `resources/views/errors`.
- evidência:
  - Login e forgot-password em inglês ("Forgot your password?", "We have emailed…").
  - Mensagens de validação e paginação em inglês ("Showing 1 to 12 of 17 results", "« Previous").
  - Os erros 404, 403 e 500 usam a página por omissão, em inglês e sem o layout do site. Por exemplo, a máquina vendida /catalogo/22 mostra "404 Not Found".
- impacto: tira confiança a um site todo em português. Os erros de validação do backoffice ficam em inglês e o leitor de ecrã lê o backoffice com pronúncia inglesa.
- ficheiros: `.env.production.example:17`; `config/app.php:81`; `resources/views/layouts/admin.blade.php:3`; `resources/views/auth/*`
- recomendação:
  - `APP_LOCALE=pt_PT` e `APP_FALLBACK_LOCALE=pt_PT`.
  - `php artisan lang:publish` e tradução pt.
  - Criar `resources/views/errors/{404,403,419,500,503}.blade.php` com o layout do site e link para o catálogo.
  - Publicar e traduzir as views de paginação.

#### PERF-002 — O detalhe serve o original (vários MB) como imagem principal
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 2–3 h · tarefa: T10
- estado_atual: `machine-show.blade.php:52-61` usa o URL do original. Só as listagens usam miniaturas.
- evidência: /catalogo/6 transfere 6,76 MB, dos quais 5,45 MB em imagens. O original de 4032×3024 (≈4,2 MB) é mostrado com no máximo ≈700 px de largura.
- impacto: carregamento lento em rede móvel e consumo de dados do cliente. O LCP real em 4G não foi medido (Não confirmado).
- ficheiros: `resources/views/site/machine-show.blade.php:52-61,176`; `app/Services/ThumbnailService.php`
- recomendação: gerar um derivado "large" (1600 px, q80, sem EXIF) no `ThumbnailService` e usá-lo no detalhe e na lightbox, com `srcset` (600w/1600w). Não voltar a servir o original.

#### A11Y-001 — Campos do backoffice sem rótulo associado
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 2 h · tarefa: T17
- estado_atual:
  - Em `_form.blade.php:152-187` os `<label>` não têm `for`.
  - Os selects de filtro da lista (`index.blade.php:60,78`) não têm label.
  - O select de estado (`:213-214`) é invisível e não tem nome acessível.
  - O input de renomear categoria não tem label.
- evidência: axe `select-name` (critical, ×2) na lista, criar e editar; `label` (critical, ×5) nas categorias.
- impacto: o leitor de ecrã anuncia os campos sem nome, e tocar no rótulo não foca o campo.
- ficheiros: `resources/views/admin/machines/_form.blade.php:152-187`; `resources/views/admin/machines/index.blade.php:60,78,213`; `resources/views/admin/categories/index.blade.php`
- recomendação: pares `for`/`id` em todos os campos, `aria-label` nos filtros e no seletor de estado, e `<label class="sr-only">` no campo da categoria.

#### A11Y-002 — Lightbox e galeria sem gestão de foco nem estado
- prioridade: P2 · confiança: Confirmado (código) · bloqueia_deploy: Não · esforço: 2 h · tarefa: T17
- estado_atual: o diálogo (`machine-show.blade.php:172-177`) tem `role="dialog"` e `aria-modal`, mas o JS (`:208-228`) abre e fecha sem mover o foco, sem o prender e sem o devolver. Todas as miniaturas (`:85-91`) têm o mesmo `aria-label="Ver imagem adicional"` e a seleção só é visual.
- impacto: quem usa teclado abre a imagem e o Tab continua a percorrer a página escondida atrás do overlay. Ao fechar, perde a posição. O leitor de ecrã não distingue as miniaturas.
- ficheiros: `resources/views/site/machine-show.blade.php:85-91,172-177,208-228`
- recomendação: usar `<dialog>` nativo com `showModal()`, ou, na implementação atual, focar o botão Fechar ao abrir, prender o Tab e devolver o foco ao trigger ao fechar. Nas miniaturas, `aria-label="Ver imagem 2 de 5"` e `aria-pressed`/`aria-current` na ativa.

#### A11Y-003 — Contraste insuficiente, incluindo o CTA principal
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T17
- evidência (axe color-contrast, serious; o mínimo AA para este texto é 4,5:1):

  | Elemento | Cores | Contraste | Onde |
  |---|---|---|---|
  | Botões "Contactar no WhatsApp" / enviar | branco sobre emerald-600, 14 px | 3,76:1 | detalhe e contacto |
  | Selo "Negociável" | branco sobre #b56824, 12 px | 4,22:1 | cartões do catálogo |
  | Tagline "MÁQUINAS DE COSTURA" | stone-400 sobre stone-50, 10 px | 2,41:1 | cabeçalho do site |
  | Textos do backoffice | stone-400 sobre branco, 11–12 px | 2,52:1 | rodapé da barra e rótulos de secção |
- impacto: o CTA de conversão falha AA, e o texto pequeno fica difícil de ler ao sol no telemóvel.
- ficheiros: `resources/views/site/machine-show.blade.php:125`; `resources/views/site/contact.blade.php:135`; `resources/views/components/ui/badge.blade.php` (variante solid); `resources/views/layouts/site.blade.php:67`; `resources/views/layouts/admin.blade.php:98`
- recomendação: emerald-700 nos botões (≈5,5:1), brand-700 no selo, stone-500 ou stone-600 nos textos pequenos.

#### SEO-001 — Título igual em todas as páginas e sem metadados de partilha
- prioridade: P2 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 2–3 h · tarefa: T18
- estado_atual: `layouts/site.blade.php:7` tem `<title>` fixo com o nome do negócio. Não há meta description, canonical, Open Graph nem Twitter Card.
- evidência: `metrics.json`: title "MaquiVeloso" em todas as páginas; metaDesc `null`; og 0; canonical `null`.
- impacto: os resultados no Google não se distinguem entre si, e as partilhas no WhatsApp e Facebook (o canal de vendas) aparecem sem foto nem descrição da máquina.
- ficheiros: `resources/views/layouts/site.blade.php:7`; `resources/views/site/*.blade.php`
- recomendação:
  - `@yield('title')` com o formato "{Máquina} | MaquiVeloso".
  - `@yield('meta_description')` com os primeiros 155 caracteres da descrição.
  - `og:title`, `og:description`, `og:image` (derivado da foto principal) e `og:type=product`.
  - Canonical sem query string de filtros.

### 4.4 P3

#### SEC-006 — AdminUserSeeder protegido por lista de exclusão
- prioridade: P3 · confiança: Confirmado (código) · bloqueia_deploy: Não · esforço: 0,25 h · tarefa: T20
- estado_atual: `AdminUserSeeder.php:21` só aborta em `environment('production')`. O `DatabaseSeeder.php:23` usa uma lista de inclusão (`['local','testing']`).
- impacto: `db:seed --class=AdminUserSeeder` num ambiente `staging` cria admin@maquiveloso.com com a password "password".
- ficheiros: `database/seeders/AdminUserSeeder.php:21`
- recomendação: `if (! app()->environment(['local', 'testing'])) return;` e um teste para `staging`.

#### SEC-007 — Vulnerabilidades npm em dependências de desenvolvimento
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,5 h · tarefa: T01
- estado_atual: `npm audit` encontra 10 vulnerabilidades: 2 critical (concurrently → shell-quote), 6 high (vite, rollup, postcss, picomatch, nanoid, browserslist), 1 moderate e 1 low. Todas têm correção disponível. `npm audit --omit=dev` dá 0.
- impacto: nada disto chega à produção (`public/build` é estático). O risco limita-se à máquina de desenvolvimento e ao CI.
- ficheiros: `package.json`; `package-lock.json`
- recomendação: `npm audit fix`, depois `npm run build` e os testes.

#### FUNC-009 — Remover categoria não diz quantas máquinas ficam sem categoria
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T21
- estado_atual: o `confirm()` pergunta só "Remover esta categoria?" (`categories/index.blade.php:94-95`). A FK tem `nullOnDelete` (`create_machines_table.php:14-17`) e a lista não mostra contagens.
- impacto: as máquinas passam sem aviso a "sem categoria" e deixam de aparecer no filtro por categoria.
- ficheiros: `app/Http/Controllers/Admin/CategoryController.php:50-56`; `resources/views/admin/categories/index.blade.php:94-95`
- recomendação: usar `withCount('machines')`, mostrar a contagem na lista e na confirmação, ou impedir a remoção de categorias com máquinas.

#### FUNC-010 — Ordenação do catálogo depende de JavaScript
- prioridade: P3 · confiança: Confirmado (código) · bloqueia_deploy: Não · esforço: 1 h · tarefa: T15
- estado_atual: o select `sort_option` (`catalog.blade.php:83-90`) não tem `name`; os campos escondidos `sort` e `dir` (`:91-92`) são preenchidos por JS.
- impacto: sem JS, "Aplicar filtros" ignora a ordenação escolhida.
- ficheiros: `resources/views/site/catalog.blade.php:83-92`; `app/Http/Controllers/Site/CatalogController.php`
- recomendação: dar `name="sort"` ao select com valores compostos (`price_asc`, …) e interpretá-los no controller.

#### FUNC-011 — Sem limite total de imagens por máquina
- prioridade: P3 · confiança: Confirmado (código) · bloqueia_deploy: Não · esforço: 0,5 h · tarefa: T11
- estado_atual: há `images max:8` por pedido, mas cada edição pode acrescentar mais sem limite.
- impacto: galerias muito longas e disco a crescer. Risco baixo, porque só o admin carrega imagens.
- ficheiros: `app/Http/Controllers/Admin/MachineController.php:175`
- recomendação: validar que as imagens existentes mais as novas não passam de 12.

#### FUNC-012 — Validação sem limite superior para preço e descrição
- prioridade: P3 · confiança: Provável (não reproduzido em MariaDB) · bloqueia_deploy: Não · esforço: 0,25 h · tarefa: T12
- estado_atual: o preço é validado com `numeric|min:0`, sem máximo, e a coluna é `decimal(10,2)`. A descrição é `string` sem máximo, numa coluna `text`.
- impacto: em MariaDB com strict mode, um preço ≥ 100 000 000 ou uma descrição com mais de 64 KB dá erro SQL (500). O SQLite aceita.
- ficheiros: `app/Http/Controllers/Admin/MachineController.php:170-172`; `database/migrations/2026_01_21_182517_create_machines_table.php:22,27`
- recomendação: validar `max:99999999.99` no preço e `max:10000` na descrição.

#### FUNC-013 — Máquinas reservadas desaparecem do site e o link partilhado dá 404
- prioridade: P3 · confiança: Confirmado (comportamento); Não confirmado como defeito (pode ser intencional) · bloqueia_deploy: Não · esforço: 1–2 h · tarefa: T21
- estado_atual: o catálogo filtra `status = available` (`CatalogController.php:52`) e o detalhe faz `abort_if(status !== 'available', 404)` (`:78`).
- impacto: um link partilhado por WhatsApp de uma máquina que passou a reservada abre um 404 em inglês. O site também não mostra vendas feitas.
- ficheiros: `app/Http/Controllers/Site/CatalogController.php:52,78`
- recomendação: decisão de produto. Sugestão: mostrar as reservadas com selo "Reservada" e o CTA adaptado; nas vendidas, página com aviso e link para máquinas semelhantes.

#### FUNC-014 — Datas mostradas em UTC
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,5 h · tarefa: T21
- estado_atual: `config/app.php:68` usa `'timezone' => 'UTC'`, e a lista do backoffice mostra `created_at` em d/m/Y (`index.blade.php:182`).
- impacto: no horário de verão, uma máquina criada entre as 00:00 e a 01:00 aparece com a data do dia anterior.
- ficheiros: `config/app.php:68`; `resources/views/admin/machines/index.blade.php:182`
- recomendação: converter na apresentação (`->timezone('Europe/Lisbon')`), ou mudar a timezone da app com o cuidado devido aos dados existentes.

#### UI-004 — Fotos verticais cortadas no detalhe
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,25 h · tarefa: T10
- estado_atual: `machine-show.blade.php:51,61` põe a imagem num contentor `aspect-[4/3]` com `object-cover`.
- evidência: na foto vertical da máquina 8 ficam cortados ≈44 % da altura (22 % em cima e 22 % em baixo). A lightbox (`object-contain`, `:176`) mostra a foto inteira.
- impacto: parte da máquina fica escondida na vista principal.
- ficheiros: `resources/views/site/machine-show.blade.php:51,61`
- recomendação: usar `object-contain` com fundo neutro no detalhe e manter `cover` nos cartões.

#### UI-005 — Seletor de estado invisível na lista do backoffice
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T05
- estado_atual: `index.blade.php:213-214` tem `<select class="absolute inset-0 h-9 w-9 cursor-pointer opacity-0">` sobre um botão de ícone.
- impacto: a mudança rápida de estado não se descobre, e o controlo não mostra o valor atual (ver também A11Y-001).
- ficheiros: `resources/views/admin/machines/index.blade.php:205-225`
- recomendação: um select visível, ou um menu com o rótulo "Mudar estado", e confirmação visual; a resposta JSON já existe.

#### UI-006 — Microcopy e marca inconsistentes no backoffice
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,5 h · tarefa: T21
- estado_atual:
  - O backoffice trata por "tu" ("Ainda não tens máquinas. Clica em…", `index.blade.php:230`; "Podes selecionar várias imagens (Ctrl).", `_form.blade.php:144`) e o site por "você".
  - A marca aparece como "Maquiveloso" (`admin.blade.php:22`) e como "MaquiVeloso".
  - "(Ctrl)" não se aplica a telemóvel nem a macOS.
- impacto: aspeto pouco cuidado e instruções erradas no telemóvel.
- ficheiros: `resources/views/admin/machines/index.blade.php:230`; `resources/views/admin/machines/_form.blade.php:144`; `resources/views/layouts/admin.blade.php:22`
- recomendação: escolher um registo e mantê-lo em todo o backoffice, usar "MaquiVeloso" em todo o lado e escrever "Pode selecionar várias imagens.".

#### UI-007 — Definições: morada e horário numa só linha; perfil sem acesso no menu
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T21
- estado_atual: `admin/settings.blade.php:45,57` usa `input type="text"` para a morada e o horário, e `layouts/admin.blade.php` não tem link para /profile.
- impacto: um horário com várias linhas é difícil de escrever, e o dono não encontra onde mudar a password.
- ficheiros: `resources/views/admin/settings.blade.php:45,57`; `resources/views/layouts/admin.blade.php`
- recomendação: textarea com `nl2br(e())` na apresentação e um link "Perfil e password" no menu.

#### PERF-003 — Login e perfil carregam fontes externas (fonts.bunny.net)
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,5 h · tarefa: T16
- estado_atual: `layouts/guest.blade.php:12-13` e `layouts/app.blade.php:11-12` (Breeze) carregam a Figtree de fonts.bunny.net. O site e o backoffice usam fontes do sistema.
- impacto: pedido a um terceiro, que recebe o IP do visitante (Requer validação jurídica; a Bunny está sediada na UE). Bloqueia a renderização e dá um estilo diferente do resto.
- ficheiros: `resources/views/layouts/guest.blade.php:12-13`; `resources/views/layouts/app.blade.php:11-12`
- recomendação: remover os links e usar o stack de sistema do `tailwind.config.js`, idealmente com login e perfil no layout da marca.

#### A11Y-004 — Lista de contactos com estrutura `<dl>` inválida
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,5 h · tarefa: T17
- estado_atual: em `contact.blade.php:45`, os filhos `<div>` do `<dl>` contêm um `<span>` (ícone) e outro `<div>` com dt/dd.
- evidência: axe `definition-list` (1) e `dlitem` (10), serious.
- ficheiros: `resources/views/site/contact.blade.php:45-100`
- recomendação: cada `<div>` do `dl` só com dt/dd (o ícone dentro do dt), ou trocar por `<ul>`.

#### A11Y-005 — Hierarquia de cabeçalhos e marcos incompleta
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T17
- evidência (axe):
  - `heading-order`: h3 sem h2 anterior nos cartões de valor (`home.blade.php:91`) e nos cartões do catálogo.
  - Login sem h1 e sem `<main>` (`layouts/guest.blade.php`).
  - Perfil sem h1; o botão do menu mobile não tem nome (`layouts/navigation.blade.php:56`).
  - Paginação com `aria-label` num `<span>` (aria-prohibited-attr).
- ficheiros: `resources/views/site/home.blade.php:91`; `resources/views/components/machine/card.blade.php:39`; `resources/views/layouts/guest.blade.php`; `resources/views/layouts/navigation.blade.php:56`
- recomendação: acertar os níveis, pôr h1 e `<main>` nas views do Breeze, `aria-label` no botão do menu e publicar as views de paginação.

#### A11Y-006 — Alvos de toque pequenos
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,5 h · tarefa: T17
- evidência:
  - Links da barra superior (telefone e email): 20 px de altura.
  - Breadcrumb: 17 px.
  - Número de WhatsApp na página de contacto: 17 px.
  - "Forgot your password?": 20 px.
  - Checkbox "Remember me": 16×16 px.
  - Por página pública há 3 a 12 alvos com menos de 24 px.
- impacto: a WCAG 2.2 (2.5.8, AA) pede 24×24 px ou espaçamento equivalente. Os links dentro de frases estão isentos; os da barra superior não.
- ficheiros: `resources/views/layouts/site.blade.php` (barra superior, rodapé); `resources/views/site/machine-show.blade.php` (breadcrumb); `resources/views/auth/login.blade.php`
- recomendação: `py-1.5` ou `min-h-6` nos links isolados e checkbox de 20 px com label clicável.

#### CODE-001 — BOM UTF-8 em duas views
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,25 h · tarefa: T20
- estado_atual: `resources/views/site/home.blade.php:1` e `resources/views/site/contact.blade.php:1` começam por EF BB BF, e as respostas de `/` e `/contacto` também. O README exige "UTF-8 without BOM".
- impacto: nenhum efeito visual verificado (o Chrome renderiza em CSS1Compat e UTF-8), mas viola a regra do projeto.
- ficheiros: `resources/views/site/home.blade.php:1`; `resources/views/site/contact.blade.php:1`
- recomendação: regravar os dois ficheiros sem BOM e acrescentar ao CI uma verificação (`grep -rl $'\xEF\xBB\xBF' resources`).

#### CODE-002 — Página inicial numa closure que engole exceções
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,5 h · tarefa: T20
- estado_atual: `routes/web.php:23-44` chama `Schema::hasTable('machines')` em cada pedido e tem um `catch (\Throwable)` que não regista nada.
- impacto: se a BD falhar, a página inicial mostra "sem destaques" e não fica nada no log. Há uma query extra por pedido.
- ficheiros: `routes/web.php:23-44`
- recomendação: mover para um `HomeController`, retirar o `hasTable` e deixar a exceção propagar-se (ou chamar `report($e)`).

#### CODE-003 — As definições gravam chaves legadas duplicadas
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T20
- estado_atual: `SettingsController.php:44-49` grava `phone`, `email` e `location` além das chaves `contact_*`.
- impacto: duas fontes de verdade que podem divergir.
- ficheiros: `app/Http/Controllers/Admin/SettingsController.php:44-49`
- recomendação: passar todas as leituras para `contact_*` e apagar as chaves legadas com uma migration de dados.

#### CODE-004 — Restos do skeleton Laravel
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T20
- estado_atual:
  - `README.md` e `CHANGELOG.md` são os do Laravel, e o `composer.json:3` tem `"name": "laravel/laravel"`.
  - Três workflows do skeleton: `issues.yml`, `pull-requests.yml` (com `pull_request_target`, workflow remoto `laravel/.github@main` e permissão `pull-requests: write`) e `update-changelog.yml`.
  - `package.json:11` tem `@tailwindcss/vite`, plugin do Tailwind 4 sem uso (o projeto usa Tailwind 3 via PostCSS).
  - FQCN inline em `CatalogController.php:71`.
- impacto: o README não explica o projeto (instalação, `admin:create`, deploy); há workflows de terceiros a correr sem necessidade; há dependências mortas.
- ficheiros: `README.md`; `CHANGELOG.md`; `composer.json:3`; `.github/workflows/{issues,pull-requests,update-changelog}.yml`; `package.json:11`; `app/Http/Controllers/Site/CatalogController.php:71`
- recomendação:
  - README próprio, com link para `DEPLOYMENT.md`.
  - Apagar o CHANGELOG e os três workflows.
  - Mudar o nome do pacote.
  - `npm remove @tailwindcss/vite`.
  - Usar `use App\Models\Category`.

#### CODE-005 — JavaScript inline nas views
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 2 h · tarefa: T08
- estado_atual: há `<script>` inline em `site/machine-show.blade.php` (galeria e lightbox, linhas 181-230), `site/catalog.blade.php`, `site/contact.blade.php`, `admin/machines/index.blade.php` e `admin/machines/_form.blade.php`.
- impacto: impede uma CSP sem `'unsafe-inline'` (SEC-002), e este código fica fora do Vite (sem minificação nem cache).
- ficheiros: os cinco acima
- recomendação: passar o código para módulos em `resources/js`, inicializados por atributos `data-*`.

#### DB-001 — Compatibilidade com MariaDB não verificada nesta versão
- prioridade: P3 · confiança: Não confirmado · bloqueia_deploy: Precisa de confirmação · esforço: 2 h · tarefa: T07
- estado_atual: a produção prevê MySQL/MariaDB (`DB_CONNECTION=mysql`) e os testes correm em SQLite. A ronda anterior (41f699d) validou as migrations em MariaDB. Nesta ronda o Docker não estava disponível. A única migration nova (`2026_06_14_120000_add_thumb_path_to_machine_images_table.php`) acrescenta uma coluna string nullable.
- impacto: risco baixo. O enum de estado e o decimal comportam-se de forma diferente consoante o motor (ver FUNC-012).
- ficheiros: `database/migrations/*`; `.env.production.example`
- recomendação: ensaio em staging com MariaDB: `migrate --force`, `admin:create`, upload de fotos e `machines:generate-thumbnails`.

#### TEST-001 — Os testes dependem do build Vite em local
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,25 h · tarefa: T19
- estado_atual: `tests/TestCase.php` não chama `withoutVite()`. Sem `public/build`, falham 27 testes (ViteManifestNotFoundException). O `tests.yml` faz `npm run build` antes, por isso o CI não é afetado.
- impacto: `php artisan test` num clone novo falha e confunde quem contribui.
- ficheiros: `tests/TestCase.php`
- recomendação: `$this->withoutVite()` no `setUp()` do TestCase, ou documentar no README.

#### TEST-002 — Os testes não cobrem os riscos encontrados
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 0,5 dia · tarefa: T19
- estado_atual: 98 testes passam. Não há testes para:
  - orientação EXIF;
  - imagens muito grandes;
  - entrega do email de reposição;
  - os 4 casos de preço renderizados nas views (o `MachinePricePresentationTest` só testa o modelo);
  - conteúdo visível sem JS;
  - formato do WhatsApp.
- impacto: FUNC-001, FUNC-003 e FUNC-006 passaram com a suite verde.
- ficheiros: `tests/Feature/*`; `tests/Unit/*`
- recomendação:
  - Testes de feature a /catalogo e /catalogo/{id} que verifiquem "1.234,56 €", "Preço negociável", "Sob consulta" e o selo "Negociável".
  - Teste do `ThumbnailService` com uma fixture Orientation=6.
  - Teste do limite de píxeis.
  - `Notification::fake()` para o `ResetPassword`.

#### SEO-002 — Sem sitemap.xml
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: 1 h · tarefa: T18
- estado_atual: `public/robots.txt` permite tudo e não tem `Sitemap:`; não existe sitemap.
- ficheiros: `public/robots.txt`; `routes/web.php`
- recomendação: rota `/sitemap.xml` com a página inicial, catálogo, contacto e máquinas disponíveis (com `lastmod`), linha `Sitemap:` no robots.txt e registo no Google Search Console.

#### SEO-003 — favicon.ico vazio e ícones em falta
- prioridade: P3 · confiança: Confirmado · bloqueia_deploy: Não · esforço: incluído em T06 · tarefa: T06
- estado_atual: `public/favicon.ico` tem 0 bytes. O único ícone é o PNG de 1,17 MB (`site.blade.php:8`). Não há apple-touch-icon nem manifest.
- impacto: quem pede /favicon.ico (crawlers, alguns browsers, separadores do backoffice e do login) recebe um ficheiro vazio.
- ficheiros: `public/favicon.ico`; `resources/views/layouts/site.blade.php:8`; `resources/views/layouts/admin.blade.php`
- recomendação: `favicon.ico` 32×32, PNG 192 e 512, `apple-touch-icon` 180, e ligá-los em todos os layouts.

---

## 5. Feature Coverage

| Funcionalidade | Estado | Testes | Notas |
|---|---|---|---|
| Página inicial com hero e destaques | Implementado | Sim | Destaques em `featured=true` + `available` (máx. 6); UI-002 |
| Catálogo: pesquisa, categoria, preço mín./máx., ordenação, paginação 12 | Implementado | Sim | Pesquisa só por nome (FUNC-004); ordenação depende de JS (FUNC-010); paginação em inglês (UI-003) |
| Detalhe: galeria, lightbox, breadcrumb, especificações, WhatsApp | Implementado | Parcial | A11Y-002, PERF-002, UI-004 |
| Regras de preço (4 casos) | Implementado | Modelo sim, view não | Ver tabela abaixo; FUNC-006 |
| Contacto: dados e formulário que abre o WhatsApp | Implementado | Sim | FUNC-007, A11Y-004 |
| Backoffice: listar, filtrar, criar, editar máquinas | Implementado | Sim | UI-001, A11Y-001 |
| Backoffice: apagar máquina | Parcial | Sim (rota) | Só numa página sem link (FUNC-005) |
| Marca/modelo | Parcial | Não | Colunas e validação existem; faltam os campos (FUNC-004) |
| Upload múltiplo + miniaturas GD | Implementado com defeitos | Sim | FUNC-001, FUNC-003, SEC-004 |
| Imagem principal | Implementado | Sim (5) | Transação; outra máquina → 404 (testado) |
| Mudança rápida de estado | Implementado | Sim | Controlo invisível (UI-005) |
| Categorias CRUD | Implementado | Sim | FUNC-009 |
| Definições de contacto e negócio | Implementado | Sim | FUNC-007, UI-007, CODE-003 |
| Autenticação e área admin (`auth` + `admin`) | Implementado | Sim | Login com throttle |
| Registo público | Desligado | Sim | GET/POST /register → 404 |
| Recuperação de password | Parcial | Sim (Breeze) | Não entrega em produção (FUNC-002); SEC-003 |
| `php artisan admin:create` | Implementado | Sim (5) | Não repõe a password de um admin existente |
| `php artisan machines:generate-thumbnails {--force}` | Implementado | Sim (6) | Não corrige a orientação |
| Mensagens flash | Implementado | Sim (4) | `role=status` / `role=alert` |
| Perfil (Breeze) | Implementado | Sim | Em inglês, sem link no menu (UI-007) |
| Páginas legais | Em falta | — | FUNC-008 |
| Páginas de erro próprias | Em falta | — | UI-003 |

**Regras de preço, verificadas no HTML renderizado:**

| Caso | Dados | Cartão | Detalhe | Resultado |
|---|---|---|---|---|
| 1. Preço + negociável | 1234,56 · negociável (máquina 6) | Selo "Negociável" + "1.235 €" | "1.235 €" + "Valor sujeito a negociação." | Lógica correta; valor arredondado (FUNC-006) |
| 2. Só preço | 149 (máquina 5) | "149 €" | "149 €" | Correto |
| 3. Sem preço + negociável | — · negociável (máquina 7) | Selo "Negociável" + "Preço negociável" | "Preço negociável" + "Contacte-nos para uma proposta personalizada." | Correto |
| 4. Sem preço, não negociável | — (máquina 8) | "Sob consulta" | "Sob consulta" + "Contacte-nos para uma proposta personalizada." | Correto |
| Limite: preço 0 | 0 (máquina 11) | "0 €" | "0 €" | Incorreto (FUNC-006) |

A lógica vive em `Machine::priceState()` (`app/Models/Machine.php:85`) e no componente `x-machine.price`. A normalização da entrada ("1.234,56", "1 234,56") é partilhada pelo backoffice e pelo filtro público através de `app/Support/PriceNormalizer.php`.

---

## 6. Security

**Controlos verificados sem problemas:**
- Registo público desligado (`config/auth.php:126`, `REGISTRATION_ENABLED=false`); /register responde 404.
- `/admin` protegido por `auth` + `admin` (`routes/web.php:83`, `EnsureUserIsAdmin`).
- CSRF ativo em todos os formulários (middleware por omissão).
- Upload: um SVG com `onload` e um ficheiro de texto com extensão .jpg foram rejeitados pela validação `image`, sem criar registos.
- Bindings com scope: PATCH `feature` e DELETE sobre uma imagem de outra máquina → 404.
- Nenhum `{!! !!}` nas views da aplicação; o output Blade é todo escapado.
- Login com throttle (LoginRequest do Breeze).
- Seeders sem efeito em produção (`DatabaseSeeder` com lista de inclusão; `ProductionSafetyTest`).
- `.env` fora do git; sem segredos no histórico; `.claude/` removido do repositório e ignorado.
- Sem `dd()`, `dump()` nem `console.log` no código.
- `.env.production.example` com `APP_DEBUG=false` e `SESSION_SECURE_COOKIE=true`.
- Cookies: `XSRF-TOKEN` e sessão (`httponly`, `samesite=lax`).

**Achados:** SEC-001 (P1), SEC-002, SEC-003, SEC-004, SEC-005 (P2), SEC-006, SEC-007 (P3). Com impacto de segurança há também FUNC-002 (recuperação de acesso) e CODE-005 (bloqueia a CSP).

---

## 7. UI / Responsive

**Site público:**
- Sem overflow horizontal em 375, 390, 768, 1024 e 1440 px em nenhuma página (página inicial, catálogo, catálogo vazio, detalhe com galeria, detalhe com foto rodada, detalhe sem foto, contacto, 404 e login).
- CLS 0 em todas as combinações.
- Hierarquia visual clara, CTAs visíveis na primeira dobra a 375 px e menu mobile funcional.
- O estado vazio do catálogo é contextual ("sem resultados para…").

**Backoffice:**
- Não é responsivo (UI-001). Medições de scrollWidth na secção 3.
- A 375 e 390 px todas as páginas têm scroll horizontal (exceto o perfil Breeze).
- A 768 e 1024 px só a lista de máquinas transborda (1188 px).
- A 1440 px nenhuma página transborda.

**Problemas visuais:**
- Miniaturas rodadas (FUNC-001).
- Fotos verticais cortadas no detalhe (UI-004).
- Conteúdo invisível sem JS (UI-002).
- Seletor de estado invisível (UI-005).
- Microcopy do backoffice (UI-006) e definições (UI-007).
- Inglês e páginas de erro por omissão (UI-003).

**Método:** Playwright com Chrome real. Screenshots de página inteira e da primeira dobra em 5 larguras × 17 páginas. Scroll com a roda do rato para disparar o scroll-reveal.

**Nota de método:** o scroll programático (`window.scrollTo` em saltos) não dispara o IntersectionObserver no Chrome headless. Os screenshots de página inteira tirados assim mostravam secções em branco que não acontecem com scroll real. Esse artefacto não foi contado como defeito; o defeito real está em UI-002.

---

## 8. Accessibility

- **Bom:**
  - Skip link.
  - `lang="pt"` no site.
  - Menu mobile com `aria-expanded`.
  - Labels no catálogo.
  - `prefers-reduced-motion` respeitado no CSS e no JS.
  - Foco visível nos componentes `x-ui.*`.
  - Flash com `role=status` e `role=alert`.
  - `alt` com o nome da máquina.
  - Lightbox fecha com Esc.
- **Achados:** A11Y-001, A11Y-002, A11Y-003 (P2); A11Y-004, A11Y-005, A11Y-006 (P3).
- **Relacionados:** UI-003 (`lang="en"` no backoffice) e UI-005.
- **Ferramenta:** axe-core 4.13.0 com as regras wcag2a, wcag2aa, wcag21a, wcag21aa, wcag22aa e best-practice, a 375 e 1440 px. Teclado e foco da lightbox verificados por leitura do código.
- **Não verificado:** teste com leitor de ecrã real (VoiceOver/NVDA) — Não confirmado.

---

## 9. Performance

| Página (primeira visita, sem compressão) | Total | Imagens | Queries SQL |
|---|---|---|---|
| Página inicial | 2,51 MB | 1,19 MB | 14 |
| Catálogo (12 cartões) | 2,58–2,67 MB | 1,24–1,33 MB | 10 |
| Detalhe com 5 fotos | 6,76 MB | 5,45 MB | 7 |
| Detalhe sem foto | 2,47 MB | 1,17 MB | 7 |
| Contacto | 2,47 MB | 1,17 MB | 4 |

- **Bom:**
  - Miniaturas de 600 px nas listagens.
  - `loading="lazy"`, `width` e `height` em `x-ui.image`.
  - `fetchpriority="high"` na imagem principal.
  - Eager loading (`firstImage` e `featuredImage`), sem N+1.
  - Índices em `status`, `category_id`, `price` e `created_at`.
  - JS de 16,5 kB e CSS de 11,5 kB (gzip).
- **Achados:** PERF-001 (P1), PERF-002 (P2), PERF-003 (P3); FUNC-003 (memória).
- **Não confirmado:**
  - O LCP real em rede móvel não foi medido (o localhost dá valores sem significado).
  - A compressão e a cache do Nginx de produção não foram observadas.

---

## 10. Architecture

**Arquitetura:** monólito Laravel com Blade, Alpine e Tailwind; área pública e backoffice no mesmo projeto.

**Separação de responsabilidades:**
- Controllers em `Site/` e `Admin/`.
- Serviço de imagens `app/Services/ThumbnailService.php`, partilhado pelo upload, pela remoção e pelo comando.
- Normalização de preço `app/Support/PriceNormalizer.php`, partilhada pelo backoffice e pelo filtro.
- Estados configuráveis em `config/machines.php`.
- Regra de preço em `Machine::priceState()`.
- Componentes Blade `x-machine.*` e `x-ui.*`.
- Composer de settings nas views `layouts.site` e `site.*`.

**Pontos fortes:** uma fonte de verdade para as regras de preço, os estados e as miniaturas. Os comandos Artisan cobrem o arranque em produção e o backfill.

**Fraquezas:**
- Closure na página inicial (CODE-002).
- JS inline (CODE-005).
- Chaves de settings duplicadas (CODE-003).
- Upload sem transação (FUNC-003).
- Restos do skeleton (CODE-004).
- BOM (CODE-001).
- Livewire sem uso (SEC-005).

---

## 11. Database

**Esquema:** 10 migrations.
- Tabelas: `users` (com `is_admin`), `categories`, `machines`, `machine_images` (com `thumb_path` e `is_featured`), `settings`, `sessions`, `cache`, `jobs` e `password_reset_tokens`.
- `machines.category_id` → FK nullable com `nullOnDelete`.
- `status` é um enum (`available`, `reserved`, `sold`, `inactive`).
- `price` é `decimal(10,2)` nullable.
- Índices em `status`, `category_id`, `price` e `created_at`.

**Em produção:**
- `SESSION_DRIVER`, `CACHE_STORE` e `QUEUE_CONNECTION` usam a base de dados.
- Os backups estão documentados (mysqldump + `storage/app/public`, `DEPLOYMENT.md` §9), com teste de restauro recomendado.

**Achados:** DB-001 (MariaDB não reconfirmada), FUNC-012 (limites), FUNC-009 (FK nullOnDelete silenciosa).

**Ambiente local do utilizador:** a migration `2026_06_14_120000_add_thumb_path_to_machine_images_table` está Pending. Não foi aplicada, porque esta auditoria não altera dados.

---

## 12. Tests

- **`php artisan test`, com build:** 98 passam, 1 ignorado (ramo sem GD), 273 asserções, 2,08 s.
- **Sem `public/build`:** 27 falham (TEST-001).
- **Suites novas desde a última auditoria:**
  - `ProductionSafetyTest` (4), `MachineThumbnailCommandTest` (6), `MachineMainImageTest` (5).
  - `MachinePricePresentationTest` (5, ao nível do modelo), `AdminMachinePriceTest` (dataProvider).
  - `AdminFlashTest` (4), `AdminCreateCommandTest` (5), `tests/Unit/PriceNormalizerTest`.
- **CI:** `.github/workflows/tests.yml` faz `npm ci`, `npm run build` e `php artisan test` em PHP 8.2, 8.3 e 8.4. O estado das execuções no GitHub não foi consultado (sem `gh` CLI) — Não confirmado.
- **Achados:** TEST-001, TEST-002.

---

## 13. Dependencies

| Ecossistema | Âmbito | Resultado | Ação |
|---|---|---|---|
| Composer | produção (`--no-dev`) | 41 advisories em 14 pacotes: 12 high, 25 medium, 3 low, 1 sem severidade | `composer update` resolve dentro das constraints (SEC-001) |
| Composer | total | 45 advisories em 16 pacotes: 13 high, 25 medium, 6 low, 1 sem severidade | idem |
| npm | produção (`--omit=dev`) | 0 | — |
| npm | total | 10: 2 critical, 6 high, 1 moderate, 1 low (todas de dev e com correção disponível) | `npm audit fix` (SEC-007) |

**Advisories high em produção, por pacote:**

| Pacote | Versão instalada | Advisories | Versão alvo |
|---|---|---|---|
| league/commonmark | 2.8.0 | 8 | 2.10.3 |
| laravel/framework | 12.48.1 | 1 (CRLF na regra email) | 12.69.3 |
| symfony/mime | 7.4.0 | 1 | 7.4.19 |
| guzzlehttp/guzzle | 7.10.0 | 1 | 7.15.5 |
| symfony/http-kernel | 7.4.3 | 1 (`#[IsGranted]` com HEAD; atributo não usado pela app) | 7.4.20 |

**Pacotes sem uso:** `livewire/livewire` (SEC-005) e `@tailwindcss/vite` (CODE-004).

`composer validate --strict`: válido.

---

## 14. SEO

- **Bom:**
  - `lang="pt"`.
  - Um h1 por página pública.
  - URLs legíveis (/catalogo, /catalogo/{id}, /contacto).
  - Imagens com `alt`.
  - Sem overflow no mobile.
  - `robots.txt` permissivo.
- **Achados:** SEO-001 (P2), SEO-002, SEO-003 (P3).
- **Relacionados:** FUNC-013 (404 em máquinas reservadas), UI-003 (404 sem navegação) e PERF-001 e PERF-002 (peso das páginas).
- **Recomendado:** URLs com slug (`/catalogo/6-brother-innov-is-f420`) e JSON-LD `Product`/`LocalBusiness` (ver Missing Features).

---

## 15. Privacy

| Tema | Estado | Referência |
|---|---|---|
| EXIF/GPS nas fotos originais públicas | Exposto | SEC-004 |
| Fontes de terceiros (fonts.bunny.net) no login e perfil | Pedido a terceiro | PERF-003 — Requer validação jurídica |
| Política de privacidade, Livro de Reclamações Eletrónico, RAL, identificação da empresa | Em falta | FUNC-008 — Requer validação jurídica |
| Cookies | Só `XSRF-TOKEN` e sessão (estritamente necessários); sem analytics nem scripts de terceiros no site público | Aviso de cookies provavelmente dispensável — Requer validação jurídica |
| Formulário de contacto | Não guarda dados; abre o WhatsApp no dispositivo do visitante | — |
| Logs | Em local contêm links de reposição de password (mailer `log`); em produção com `LOG_LEVEL=warning` não | FUNC-002 |
| Menção "IVA incluído" nos preços | Ausente | FUNC-006 — Requer validação jurídica |

---

## 16. Deploy Readiness

| Item | Estado | Referência / nota |
|---|---|---|
| Build de assets (`npm ci && npm run build`) | Pronto | Build OK; `public/build` fora do git |
| Testes | Pronto | 98 passam com build |
| Migrations (SQLite) | Pronto | 10 aplicadas no clone |
| Migrations (MariaDB) | Precisa de confirmação | DB-001 |
| Arranque do admin (`admin:create`) | Pronto | Seeders bloqueados em produção |
| Registo desligado | Pronto | /register → 404 |
| `.env.production.example` | Precisa de confirmação | MAIL (FUNC-002), APP_LOCALE (UI-003), APP_URL |
| Email / recuperação de password | Precisa de confirmação | FUNC-002 (P1) |
| Limites de upload PHP/Nginx | Precisa de confirmação | DEPLOY-001 (P1) |
| Extensões PHP | Precisa de confirmação | GD e fileinfo documentadas; falta `exif` (FUNC-001) |
| `storage:link` e permissões | Pronto | `DEPLOYMENT.md` §3/§6 |
| HTTPS e cookie seguro | Pronto | `DEPLOYMENT.md` §8, `SESSION_SECURE_COOKIE=true` |
| Cabeçalhos de segurança | Precisa de correção | SEC-002 |
| Dependências | Precisa de correção | SEC-001 (P1) |
| Backups e rollback | Pronto | §9 e §13 documentados |
| Filas / cron | Pronto | Não são necessários (o ResetPassword é síncrono) |
| Miniaturas existentes após a correção EXIF | Precisa de confirmação | Correr `machines:generate-thumbnails --force` (FUNC-001) |
| Páginas legais | Precisa de confirmação | FUNC-008 |
| Ordem `optimize:clear` → `migrate` numa instalação nova | Pronto | A documentação corre `migrate` primeiro; com `CACHE_STORE=database`, `optimize:clear` antes das migrations falha ("no such table: cache") — não inverter a ordem |

**Bloqueadores:** nenhum.

---

## 17. Missing Features

### 17.1 Necessárias
- Informação legal e Livro de Reclamações Eletrónico (FUNC-008, Requer validação jurídica).
- Envio de email transacional, ou reposição de password por CLI (FUNC-002).
- Páginas de erro em português com o layout do site (UI-003).
- Campos marca e modelo no backoffice (FUNC-004).
- Ação de apagar máquina acessível (FUNC-005).

### 17.2 Recomendadas
- Metadados por página, Open Graph e sitemap (SEO-001, SEO-002).
- Derivado de imagem "large" sem EXIF (PERF-002, SEC-004).
- JSON-LD `Product` e `LocalBusiness`.
- Estado "Reservada" visível no site (FUNC-013).
- Reordenar imagens por arrastar.
- Duplicar máquina.
- Monitorização de erros (Flare, Sentry ou email de alertas) e de uptime sobre `/up`.
- Backups automatizados com teste de restauro periódico.
- Filtro por marca.

### 17.3 Opcionais
- Formulário de contacto com registo ou envio por email (alternativa ao WhatsApp).
- Analytics sem cookies, como Plausible ou Umami (Requer validação jurídica).
- URLs com slug.
- Formatos WebP/AVIF.
- Vários administradores com papéis diferentes.
- Histórico de alterações de preço e estado.
- Mapa na página de contacto (com cuidado de privacidade).

---

## 18. Action Plan

Cada achado está atribuído a uma única tarefa. As estimativas são de trabalho técnico e não incluem decisões do dono nem validação jurídica.

### Fase 1 — Antes do lançamento público (≈3 dias)

| Tarefa | Descrição | Achados | Prioridade | Esforço | Depende de |
|---|---|---|---|---|---|
| T01 | Atualizar dependências PHP e npm; remover Livewire | SEC-001, SEC-005, SEC-007 | P1 | 2–3 h | — |
| T02 | Corrigir a orientação EXIF nas miniaturas e regenerá-las | FUNC-001 | P1 | 2–3 h | — |
| T03 | Recuperação de acesso: SMTP, comando de reposição, documentação | FUNC-002 | P1 | 3–4 h | Decisão SMTP |
| T04 | Configurar e documentar limites de upload (PHP e Nginx) | DEPLOY-001 | P1 | 1–2 h | Dados do alojamento |
| T05 | Backoffice responsivo (layout, lista, seletor de estado) | UI-001, UI-005 | P1 | 1–1,5 dias | — |
| T06 | Logótipo, favicon e ícones otimizados | PERF-001, SEO-003 | P1 | 1 h | — |
| T07 | Ensaio de deploy em staging com MariaDB | DB-001 | P3 | 2 h | T01, T02, T04 |

### Fase 2 — Primeiras semanas (≈5–6 dias)

| Tarefa | Descrição | Achados | Prioridade | Esforço | Depende de |
|---|---|---|---|---|---|
| T08 | Cabeçalhos de segurança; passar os scripts inline para o Vite | SEC-002, CODE-005 | P2 | 4–5 h | — |
| T09 | Throttle e mensagem neutra na recuperação de password | SEC-003 | P2 | 1 h | T03, T16 |
| T10 | Pipeline de imagens robusta: limite de píxeis, transação, derivado "large" sem EXIF, `object-contain` | FUNC-003, PERF-002, SEC-004, UI-004 | P2 | 1 dia | T02 |
| T11 | Backoffice: marca e modelo, apagar na lista, limite total de imagens | FUNC-004, FUNC-005, FUNC-011 | P2 | 0,5 dia | T05 |
| T12 | Preço: casas decimais, preço 0, limites de validação | FUNC-006, FUNC-012 | P2 | 1,5 h | — |
| T13 | WhatsApp em formato internacional | FUNC-007 | P2 | 1–2 h | — |
| T14 | Páginas e links legais | FUNC-008 | P2 | 0,5 dia + jurista | — |
| T15 | Conteúdo e ordenação funcionais sem JavaScript | UI-002, FUNC-010 | P2 | 2 h | — |
| T16 | Português em toda a app e páginas de erro próprias | UI-003, PERF-003 | P2 | 0,5 dia | — |
| T17 | Acessibilidade: labels, foco da lightbox, contraste, estrutura, alvos de toque | A11Y-001, A11Y-002, A11Y-003, A11Y-004, A11Y-005, A11Y-006 | P2 | 1 dia | T05, T16 |
| T18 | SEO: títulos, meta description, Open Graph, canonical, sitemap | SEO-001, SEO-002 | P2 | 3–4 h | T10 (og:image) |

### Fase 3 — Manutenção (≈1,5 dias)

| Tarefa | Descrição | Achados | Prioridade | Esforço | Depende de |
|---|---|---|---|---|---|
| T19 | Testes: `withoutVite`, views de preço, EXIF, imagens grandes, reset de password | TEST-001, TEST-002 | P3 | 0,5 dia | T02, T10, T12, T15 |
| T20 | Limpeza: BOM, HomeController, settings legadas, restos do skeleton, seeder com lista de inclusão | CODE-001, CODE-002, CODE-003, CODE-004, SEC-006 | P3 | 3–4 h | T01 |
| T21 | Backoffice e produto: categorias, reservadas, fuso horário, microcopy, definições | FUNC-009, FUNC-013, FUNC-014, UI-006, UI-007 | P3 | 0,5 dia | T05 |

**Cobertura:** 49 achados em 21 tarefas, cada achado numa só tarefa.

---

## 19. Final Status

```yaml
estado_deploy: Precisa de confirmação
bloqueadores_P0: 0
P1: 6   # SEC-001, FUNC-001, FUNC-002, DEPLOY-001, UI-001, PERF-001
P2: 17
P3: 26
total: 49
maior_risco: "Fotos verticais de telemóvel aparecem de lado nas listagens (FUNC-001); com os limites por omissão do PHP e do Nginx, o upload dessas fotos também falha (DEPLOY-001)"
recomendacao: >
  Deploy técnico possível (sem P0). Lançamento público só depois da Fase 1 (T01–T07, ≈3 dias)
  e de três decisões do dono: fornecedor SMTP, limites de upload/memória do alojamento e
  validação jurídica da informação legal.
evolucao_desde_41f699d: os 3 P0 anteriores estão resolvidos; a qualidade geral subiu de forma clara
acao_local_pendente: "php artisan migrate (migration add_thumb_path Pending no ambiente local)"
```
