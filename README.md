# SiteManancial-Demo

Plataforma em PHP para a Missão Evangélica Manancial da Esperança, com landing page pública, cadastro, login, painel do usuário, player de vídeo e área administrativa de conteúdos.

## Fluxo principal
1. Acesse a landing em `index.html`.
2. Vá para `login.php` ou `php/fiel/register.php`.
3. Após autenticação, o usuário entra em `php/fiel/dashboard.php`.
4. O admin entra no mesmo fluxo e acessa `php/admin/admin_conteudos.php` para gerenciar vídeos e acompanhar usuários.

## Estrutura principal
- `index.html`: landing page pública.
- `login.php`: autenticação e entrada da aplicação.
- `php/fiel/`: área do usuário.
- `php/admin/`: área administrativa de conteúdos.
- `php/conexao.php`: conexão com o banco, `MODO_BANCO` e guard de sessão.
- `videos/`: arquivos de vídeo exibidos no player.
- `docs/`: regras, requisitos e materiais de apoio.

## Como rodar
1. Inicie o Apache e o MySQL no XAMPP.
2. Importe o banco desejado:
   - `database/Banco Igreja.txt` para a versão básica.
   - `database/Banco Igreja Robusto.sql` para a versão robusta.
3. Ajuste `php/conexao.php` se precisar trocar usuário, senha ou o modo do banco.
4. Abra `http://localhost/SiteManancial-Demo/index.html`.

## Modo simples e modo robusto
No arquivo `php/conexao.php`, altere:

```php
define('MODO_BANCO', 'simples');
```

- `simples`: usa a estrutura mais básica, indicada para apresentação e testes rápidos.
- `robusto`: usa o modelo com hash de senha, relacionamentos e organização melhorada.

## Regras importantes
- Nome não aceita números.
- E-mail precisa ser válido.
- CPF e telefone têm validação e máscara.
- Cadastro exige confirmação de senha e aceite dos termos.
- Perfil do usuário é somente leitura.
- A sessão expira por inatividade após 1 hora.
- Vídeos locais devem ficar em `videos/`.

## Login de teste
- `joao@email.com` / `123456`
- `maria@email.com` / `123456`

## Observações
- O vídeo inicial de demonstração é `videos/Neymar.MP4` quando não houver conteúdo no banco.
- O admin pode enviar, listar, editar e excluir conteúdos pela área administrativa.
- Os links de volta foram ajustados para evitar telas mortas no fluxo do usuário.

## Guia rápido para subir no GitHub

- Objetivo: organizar o repositório para facilitar revisão, deploy e colaboração.
- Não versionar arquivos grandes (vídeos) — usar armazenamento externo e referenciar em `videos/` apenas como local de desenvolvimento.
- Sugestão de pastas para o repositório (explicação curta): veja `REPO_STRUCTURE.md`.

Se for reorganizar os arquivos (mover `php/` para `src/php/`, por exemplo), crie um branch `repo-cleanup` e atualize os includes/path relativos antes de abrir o PR.