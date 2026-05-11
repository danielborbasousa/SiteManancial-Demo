Pasta `php/` — Código PHP atual

Esta pasta contém os scripts PHP que rodam o backend atualmente.
Recomendações:
- Pode ser migrada para `src/php/` para separar código do conteúdo público.
- Antes de mover, busque e atualize todos os `include`/`require` e caminhos relativos.
- Teste em um branch isolado antes de mesclar.

Arquivos importantes:
- `conexao.php` — inicialização do banco, guards de sessão e utilitários.
- `fiel/` e `admin/` — áreas do usuário e administração.
