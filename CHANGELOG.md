# Changelog

Mudanças relevantes de cada versão do IT Inventory.
Formato baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/) e versionamento [SemVer](https://semver.org/lang/pt-BR/).

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
