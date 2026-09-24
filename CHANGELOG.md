# Changelog

Mudanças relevantes de cada versão do IT Inventory.
Formato baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/) e versionamento [SemVer](https://semver.org/lang/pt-BR/).

## [Não lançado] — 2.0.0

### Adicionado
- **Ficha técnica por tipo de equipamento.** O tipo ganha uma **categoria**, e cada categoria tem sua tabela:
  - **Computador** (`computadores`): sistema operacional, processador, placa de vídeo, memória (GB, tipo DDR a DDR5/LPDDR, formato), armazenamento (GB e tipo), MAC do cabo e do Wi-Fi, portas de vídeo, outras portas e ID do AnyDesk (sem senha).
  - **Monitor** (`monitores`): polegadas, tipo de tela e portas de vídeo.
  - **Impressora** (`impressoras`): tecnologia, conexões e MAC.
  - **Dispositivo móvel** (`dispositivos_moveis`, celulares e maquininhas): IMEI 1, IMEI 2 e MAC.
  - **Genérico**: só os campos comuns (PINPad, teclado, headset...).
- Novos campos comuns do equipamento: **fabricante**, **modelo**, **identificação interna** (nome na rede, `CELUR01`...) e **nota fiscal**.
- **Patrimônio obrigatório** para equipamentos acima de R$ 1.500,00.
- Busca de equipamentos por fabricante/modelo, identificação interna e **IMEI/MAC**.
- **Chave de acesso da NF-e** (opcional) com conferência do dígito verificador e do número da nota; valor da compra, nº da nota e IMEI aceitam só números.
- **Termos e relatório por funcionário** identificam cada equipamento por tipo, fabricante/modelo, identificação interna e um resumo técnico (configuração do computador, IMEI do celular...).
- **Relatório de histórico** com dados de aquisição e a **ficha técnica completa**.
- Componente `x-form.checkbox-group` para escolhas múltiplas.
- **Tema escuro** (#3), com botão na barra superior que alterna entre claro, escuro e o tema do sistema (padrão). A escolha fica salva no navegador, é aplicada antes de a página aparecer (sem piscar) e segue a mudança de preferência do sistema. Cores com contraste WCAG AA; relatórios PDF continuam no claro.
- **Integração contínua** (GitHub Actions): a suíte de testes roda a cada push em `main`/`develop` e em cada PR, com MySQL 8 (#8).
- Testes de ficha técnica, documentos, linha do tempo e status do equipamento, validações de empresa (CNPJ, CEP, telefone, consulta ao ViaCEP) e regra de CNPJ.

### Alterado
- A descrição do equipamento passa a ser uma **observação opcional**. Listagens, termos e relatórios exibem "fabricante + modelo" (ou a descrição, nos cadastros anteriores).
- A categoria de um tipo fica travada quando já existem equipamentos desse tipo.
- A paginação das listagens passa a usar a view do projeto (`resources/views/vendor/pagination/tailwind.blade.php`), com as cores do tema.

### Corrigido
- Cadastro de tipo de equipamento falhava ao normalizar o nome.
- Exclusão de equipamento dava erro de banco (coluna ambígua) em qualquer caso; agora bloqueia só com empréstimo em aberto, indicando a movimentação.

### Atualização
```bash
php artisan migrate
npm run build
```
Os tipos existentes são classificados pelo nome (ex.: "Monitor" → Monitor; sem correspondência → Genérico). Equipamentos já cadastrados continuam válidos e pedem fabricante, modelo e ficha técnica na próxima edição.

## [1.1.0] - 2026-09-24

### Adicionado
- **Perfis de acesso** TIC e Departamento Pessoal (#4). O DP lista, consulta, cadastra e edita funcionários, vê pendências, gera o relatório de equipamentos e registra desligamentos; os demais cadastros, movimentações e devoluções são exclusivos da TIC. Menu e botões conforme o perfil e página "Acesso não permitido".
- **Data de admissão** obrigatória no cadastro de funcionário, e **desligamento com data informada** em modal, sem data futura nem anterior à admissão (#1).
- **Linha do tempo do equipamento**: cadastro, empréstimo, devolução e alteração manual de status, com usuário responsável, no relatório de histórico. Eventos anteriores à linha do tempo são reconstruídos e marcados como "Registro anterior ao histórico".
- Vínculo entre o item do termo de responsabilidade e o termo de devolução correspondente.
- Acesso ao histórico do equipamento e ao relatório de equipamentos do funcionário direto pelas listagens (#5).
- Estado (UF) da empresa como lista de seleção (#7).
- Suíte de testes automatizados (PHPUnit, banco MySQL dedicado) com 55 testes (#8, em andamento).

### Alterado
- **Novo visual**: tema claro, paleta neutra e destaque verde definido em tokens (`resources/css/app.css`), com contraste WCAG AA e foco visível por teclado. Relatórios PDF no mesmo padrão.
- Views reorganizadas em componentes Blade (`x-ui`, `x-form`, `x-table`) e menu gerado a partir de `config/navegacao.php`.
- Campo `rua` da empresa renomeado para `logradouro` (#6).
- Histórico mostra funcionários excluídos logicamente (#2).
- Dependências atualizadas.

### Corrigido
- Status "Em uso" do equipamento passa a ser controlado apenas pelas movimentações (não é mais possível alterá-lo manualmente e gerar alocação dupla).
- Envio do termo assinado: bloqueia reenvio (que sobrescrevia o arquivo), envio em movimentação cancelada ou do tipo errado, e não reabre um termo já encerrado.
- Badges de status de movimentação ("Concluída"/"Encerrada") sem cor e sem acento.
- Consultas N+1 na listagem de funcionários.
- Filtros da listagem de tipos de equipamento e scripts ausentes no build de produção (manifest do Vite).

### Atualização
```bash
php artisan migrate
php artisan db:seed --class=UsuarioSeeder   # opcional: cria dp@gmail.com (perfil DP, senha 123456)
npm run build
```
Migrations novas: `admitido_em` em funcionários, vínculo item ↔ devolução, `equipamento_historicos` (com reconstrução dos eventos existentes), marca de evento reconstruído, `rua` → `logradouro`, `perfil` em usuários (existentes ficam como TIC) e tabela `jobs`.

## [1.0.0] - 2025-11-24

Primeira versão: cadastros de empresas, setores, funcionários, usuários, tipos de equipamento e equipamentos; termos de responsabilidade e devolução com geração de PDF e upload do termo assinado; relatórios de equipamentos por funcionário e histórico do equipamento.

[1.1.0]: https://github.com/Rafael-de-Sa/it-inventory/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/Rafael-de-Sa/it-inventory/releases/tag/v1.0.0
