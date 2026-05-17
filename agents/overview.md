# Visão geral do projeto

O SiteManancial-Demo é uma aplicação web em PHP/MySQL com foco em cadastro de fiéis, autenticação, aprovação de usuários, painel administrativo e consumo de vídeos locais.

## Componentes principais

- `index.html`: entrada pública.
- `login.php`: autenticação e redirecionamento.
- `php/fiel/`: área do usuário final.
- `php/admin/`: área administrativa.
- `database/`: bancos simples e robusto.
- `docs/`: documentação funcional e estrutural.

## Bancos disponíveis

- `database/Banco Igreja.txt`: banco simples.
- `database/Banco Igreja Robusto.sql`: banco robusto.

## Fluxo principal

1. Usuário visita a landing page.
2. Faz cadastro.
3. Fica com status `pendente`.
4. Admin aprova ou rejeita.
5. Usuário aprovado entra no painel.
