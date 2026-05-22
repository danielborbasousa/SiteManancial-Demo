# SiteManancial-Demo

Sistema web em PHP desenvolvido para a Missão Evangélica Manancial da Esperança. O projeto organiza o acesso dos fiéis, o processo de aprovação de cadastro e a administração do conteúdo em um único sistema com interface responsiva.

## Como rodar o projeto

Como o professor vai receber o projeto em formato compactado, siga este passo a passo primeiro:

1. Extraia o arquivo `.zip` em uma pasta de sua preferência.
2. Copie a pasta `SiteManancial-Demo` para dentro de `htdocs` do XAMPP.
3. Inicie o Apache e o MySQL no XAMPP.
4. Abra o phpMyAdmin e importe o banco de dados simples, que é o usado por padrão no projeto.
5. Se necessário, ajuste a conexão no arquivo `php/conexao.php`.
6. Acesse `http://localhost/SiteManancial-Demo/index.html` no navegador.

Se preferir uma estrutura mais completa, o banco robusto também pode ser usado. Porém, para a execução normal do projeto e para apresentação, o banco simples é o recomendado.

## Objetivo do projeto

O sistema foi criado para centralizar em uma plataforma web:

- cadastro de usuários;
- login com perfil de acesso;
- aprovação manual de novos cadastros;
- área do fiel com páginas de apoio;
- painel administrativo para gestão do sistema;
- notificações internas para acompanhar ações importantes.

## Como o sistema funciona

O fluxo principal é simples:

1. O visitante entra na página inicial.
2. O usuário cria uma conta.
3. O cadastro fica com status `pendente`.
4. O administrador analisa o pedido e aprova ou rejeita.
5. Após aprovado, o usuário faz login e acessa a área do fiel.
6. O administrador continua responsável pela gestão de usuários, permissões, mensagens e conteúdo.

## Módulos do projeto

### Área pública

- Página inicial com apresentação do projeto.
- Tela de login.
- Tela de cadastro de fiel.

### Área do fiel

- Dashboard do usuário.
- Perfil do usuário.
- Busca de conteúdo.
- Página de notificações.
- Páginas institucionais, como Sobre e Contato.

### Área administrativa

- Aprovação e rejeição de cadastros.
- Gerenciamento de usuários.
- Gerenciamento de permissões administrativas.
- Envio de mensagens e notificações.
- Visualização e organização dos conteúdos do sistema.

## Estrutura principal

- `index.html` - página inicial pública.
- `login.php` - autenticação e redirecionamento por perfil.
- `php/conexao.php` - conexão com o banco e funções de sessão.
- `php/admin/` - páginas do administrador.
- `php/fiel/` - páginas do usuário final.
- `css/` - estilos visuais do sistema.
- `js/` - scripts de tema e interação.
- `database/` - scripts SQL com a estrutura do banco.
- `docs/` - documentação funcional e técnica.

## Banco de dados

O projeto possui duas opções de banco:

### Banco simples

Arquivo: `database/Banco Igreja.txt`

Este é o banco utilizado no fluxo principal do projeto. Ele é mais leve, fácil de importar e ideal para demonstração e uso em sala.

### Banco robusto

Arquivo: `database/Banco Igreja Robusto.sql`

Também pode ser usado, caso seja necessário testar uma estrutura mais completa. Ele possui uma modelagem mais rica e mais tabelas de apoio.

### Observação

Se trocar entre as versões, importe apenas o script correspondente e confira se a configuração do ambiente em `php/conexao.php` está apontando para o banco correto.

## Funcionalidades principais

- Cadastro de fiel com validação de dados.
- Login com redirecionamento por perfil.
- Bloqueio de contas pendentes ou negadas.
- Aprovação e rejeição de usuários pelo painel admin.
- Gerenciamento de permissões de administrador.
- Gerenciamento de usuários com edição e exclusão.
- Notificações internas para o usuário.
- Tema claro e escuro com persistência no navegador.

## Regras do sistema

- O nome não aceita números.
- O e-mail precisa ser válido.
- CPF e telefone têm validação e máscara.
- A senha exige confirmação no cadastro.
- Novos cadastros entram como `pendente` até análise do administrador.
- A sessão expira por inatividade após 1 hora.

## Configuração do banco

O arquivo `php/conexao.php` centraliza a conexão com o banco e permite alternar entre os modos simples e robusto. Caso o sistema seja movido para outro ambiente, revise esse arquivo antes de testar as telas.

## Notificações

O sistema possui notificações internas para apoiar o fluxo administrativo.

O administrador pode registrar notificações ao:

- aprovar ou rejeitar usuários;
- atualizar permissões;
- comunicar ações importantes ao usuário.

O fiel pode visualizar as notificações na área restrita do sistema.

## Documentação

Os arquivos da pasta `docs/` complementam a implementação com regras, modelagem e explicações do projeto.

## Observações finais

- O sistema foi organizado para funcionar com navegação simples e responsiva.
- Os includes e caminhos relativos devem ser mantidos com cuidado caso a pasta seja renomeada.
- Antes de fazer alterações na estrutura do banco, confira os scripts da pasta `database/` e as consultas usadas nas páginas PHP.
