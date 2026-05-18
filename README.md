# SiteManancial-Demo

Sistema web em PHP para a Missão Evangélica Manancial da Esperança. O projeto reúne landing page pública, cadastro, login, fluxo de aprovação de usuários, painel do fiel, área administrativa e reprodução de vídeos locais.

## Visão geral

O projeto foi estruturado para funcionar com dois perfis de banco de dados:

- `simples`: versão mais direta, ideal para testes, demonstração e implantação rápida.
- `robusto`: versão completa, com relacionamentos mais consistentes, tipos mais restritos e apoio a auditoria.

A aplicação identifica o modo ativo em `php/conexao.php` e adapta algumas consultas conforme a estrutura do banco.

## Fluxo principal

1. O visitante acessa a página inicial em `index.html`.
2. O novo usuário se cadastra em `php/fiel/register.php`.
3. O sistema grava o perfil com status `pendente` para aprovação do administrador.
4. O admin avalia as solicitações em `php/admin/admin_aprovar_usuarios.php`.
5. Depois de aprovado, o usuário faz login em `login.php` e acessa `php/fiel/dashboard.php`.
6. O administrador usa o painel em `php/admin/` para gerenciar conteúdos, permissões, usuários e aprovações.

## Funcionalidades atuais

- Landing page pública com acesso ao login e cadastro.
- Cadastro de fiéis com validação de nome, e-mail, CPF, telefone e confirmação de senha.
- Autenticação com bloqueio de contas pendentes ou negadas.
- Aprovação e recusa de novos cadastros pelo admin.
- Área do fiel com dashboard, perfil, notificações, busca e lista de vídeos.
- Área administrativa para conteúdos, aprovação de usuários, permissões e visão do usuário.
- Tema claro/escuro com persistência no navegador.
- Vídeos locais exibidos diretamente a partir da pasta `videos/`.

## Estrutura principal

- `index.html`: entrada pública do site.
- `login.php`: autenticação e redirecionamento por perfil.
- `php/conexao.php`: conexão com o banco, modo do banco e utilitários de sessão.
- `php/fiel/`: telas do usuário final.
- `php/admin/`: telas administrativas.
- `css/`: estilos da interface.
- `js/`: comportamento de tema e interações.
- `database/`: scripts SQL do banco simples e do banco robusto.
- `docs/`: documentação do projeto, regras e modelagem.
- `videos/`: arquivos de mídia usados no player.

## Banco de dados

### Banco simples

Arquivo: `database/Banco Igreja.txt`

Versão enxuta para subir rapidamente o sistema. Mantém a base funcional do cadastro, login, aprovação e conteúdos, com estrutura mais simples para quem quer começar sem complexidade extra.

### Banco robusto

Arquivo: `database/Banco Igreja Robusto.sql`

Versão completa, com estrutura mais rígida, chaves estrangeiras, índices, restrições e tabelas adicionais para evolução do sistema.

### Observação importante

Algumas telas possuem consultas condicionais para funcionar nos dois bancos. Se o modo do banco for alterado, confira também as rotas administrativas e o fluxo de aprovação de usuários.

## Como executar localmente

1. Inicie o Apache e o MySQL no XAMPP.
2. Importe um dos bancos disponíveis em `database/`.
3. Ajuste as credenciais em `php/conexao.php` se necessário.
4. Defina o modo do banco no arquivo `php/conexao.php`.
5. Acesse `http://localhost/SiteManancial-Demo/index.html`.

## Configuração do modo do banco

No arquivo `php/conexao.php`, o modo é controlado por uma constante semelhante a:

```php
define('MODO_BANCO', 'simples');
```

Use `simples` para a versão básica e `robusto` para a estrutura completa.

## Configuração de e-mail com Mailtrap

O envio de e-mails do sistema está centralizado em `php/conexao.php` e usa Mailtrap quando as credenciais forem preenchidas.

Preencha estas constantes no mesmo arquivo:

- `MAILTRAP_API_TOKEN`
- `MAILTRAP_INBOX_ID`

Se esses dados estiverem vazios, o sistema tenta usar `mail()` como fallback local.

O endpoint usado é o de sandbox do Mailtrap, então você precisa copiar o token e o Inbox ID da sua conta antes de testar aprovação ou recusa no admin.

## Contas de teste

- Admin: `admin@igreja.com` / `123456`
- Usuário: `joao@email.com` / `123456`
- Usuário: `maria@email.com` / `123456`

## Regras e validações

- O nome não aceita números.
- O e-mail precisa ser válido.
- CPF e telefone têm validação e máscara.
- O cadastro exige confirmação de senha e aceite dos termos.
- O perfil do usuário é somente leitura.
- A sessão expira por inatividade após 1 hora.
- Novos cadastros entram como `pendente` até a aprovação do admin.

## Documentação complementar

- `docs/regras_de_negocio_requisitos.md`: regras e requisitos do sistema.
- `docs/dicionario_de_dados.md`: descrição dos campos do banco.
- `docs/diagrama_relacional.mmd`: diagrama do relacionamento entre tabelas.
- `docs/melhorias_propostas.md`: evolução planejada do projeto.

## Observações finais

- O vídeo inicial de demonstração é usado quando não há conteúdo cadastrado.
- Os arquivos de vídeo devem ficar em `videos/` durante o desenvolvimento.
- Antes de reorganizar pastas ou alterar caminhos, revise os includes e os links relativos da aplicação.