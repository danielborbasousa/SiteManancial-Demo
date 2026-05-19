# SiteManancial-Demo

Sistema web em PHP desenvolvido para a Missão Evangélica Manancial da Esperança. O projeto reúne landing page pública, cadastro e login, fluxo de aprovação de usuários, área do fiel, painel administrativo, notificações internas e reprodução de vídeos locais.

## Visão geral

O sistema foi preparado para trabalhar com dois perfis de banco de dados:

- `simples`: versão enxuta, indicada para testes rápidos e demonstração.
- `robusto`: versão completa, com estrutura relacional mais ampla, tabelas extras e suporte a evolução do sistema.

A escolha do modo é feita em `php/conexao.php`, e algumas telas se adaptam de acordo com a estrutura disponível no banco.

## Principais módulos

### Área pública
- Landing page inicial.
- Acesso ao login e ao cadastro.
- Página de cadastro de fiel com validações de dados básicos.

### Área do fiel
- Dashboard com visão geral de vídeos e destaques da plataforma.
- Acesso aos vídeos disponíveis e à lista de conteúdos.
- Tela de perfil.
- Tela de notificações.
- Busca de conteúdos.
- Páginas institucionais como Sobre e Contato.

### Área administrativa
- Aprovação e rejeição de cadastros.
- Gerenciamento de conteúdos em vídeo.
- Gerenciamento de permissões de administradores.
- Gerenciamento de usuários.
- Visualização de usuário como experiência de teste.
- Envio de mensagens/notificações para um fiel específico ou para todos os fiéis aprovados.

## Fluxo principal

1. O visitante acessa a landing page em `index.html`.
2. O usuário faz o cadastro em `php/fiel/register.php`.
3. O sistema grava o cadastro com status `pendente`.
4. O administrador analisa a solicitação em `php/admin/admin_aprovar_usuarios.php`.
5. Após aprovado, o usuário faz login em `login.php` e entra na área do fiel.
6. O admin acessa o painel em `php/admin/dashboard.php` e administra usuários, permissões, conteúdos e mensagens.

## Funcionalidades implementadas hoje

- Cadastro de fiel com validação de nome, e-mail, CPF, telefone e confirmação de senha.
- Autenticação com redirecionamento por perfil.
- Bloqueio de acesso para contas pendentes ou negadas.
- Aprovação e recusa de usuários pelo painel admin.
- Gerenciamento de permissões de administrador.
- Gerenciamento de usuários com edição e exclusão.
- Envio de mensagens do admin para um fiel específico ou para todos os fiéis aprovados.
- Exibição de notificações para o fiel, com possibilidade de apagar notificações individuais ou todas.
- Dashboard administrativo com estatísticas e atalhos para as áreas principais.
- Vídeos locais exibidos a partir da pasta `videos/`.
- Tema claro/escuro com persistência no navegador.

## Estrutura do projeto

- `index.html` — página inicial pública.
- `login.php` — autenticação e redirecionamento por perfil.
- `php/conexao.php` — conexão com o banco, helpers de sessão e funções utilitárias.
- `php/admin/` — páginas administrativas.
- `php/fiel/` — páginas do usuário final.
- `css/` — estilos globais e específicos por tela.
- `js/` — scripts de tema e interações.
- `database/` — scripts SQL dos bancos simples e robusto.
- `docs/` — documentação do sistema, regras e modelagem.
- `videos/` — arquivos de mídia usados no player.

## Banco de dados

### Banco simples
Arquivo: `database/Banco Igreja.txt`

Versão mais compacta, útil para subir o sistema com rapidez e testar o fluxo básico de cadastro, login, aprovação e conteúdos.

### Banco robusto
Arquivo: `database/Banco Igreja Robusto.sql`

Versão mais completa, com chaves estrangeiras, índices, restrições, tabela de notificações, solicitação de acesso, sessões, recuperação de senha e metadados de mídia.

### Observação importante

Algumas telas possuem consultas condicionais para funcionar nos dois bancos. Se o modo do banco for alterado, revise também o fluxo de aprovação, as permissões administrativas e as notificações.

## Notificações

O projeto já possui suporte a notificações internas por usuário.

Atualmente:
- o admin cria notificações ao aprovar ou recusar acessos;
- o admin cria notificações ao promover ou remover permissões;
- o admin pode enviar mensagens para um fiel específico ou para todos os fiéis aprovados;
- o fiel visualiza e pode apagar notificações na área de notificações.

A tabela usada para isso é `ID_NOTIFICACAO`.

## Como executar localmente

1. Inicie o Apache e o MySQL no XAMPP.
2. Importe um dos bancos presentes em `database/`.
3. Crie um arquivo `.env` na raiz do projeto com as chaves sensíveis (ex.: credenciais do banco, chaves de serviço de e-mail). Exemplo mínimo:

```env
# Banco de dados
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=igreja_cursos

# Email (API key para provedor SMTP/Transactional)
EMAIL_API_KEY=
EMAIL_SENDER_EMAIL=seu_email@exemplo.com
EMAIL_SENDER_NAME=SiteManancial
```

4. O projeto carrega automaticamente variáveis do `.env` — não comite esse arquivo. O `.gitignore` já contém uma entrada para `.env`.
5. Defina o modo do banco em `php/conexao.php` se necessário.
5. Acesse `http://localhost/SiteManancial-Demo/index.html`.

## Configuração do modo do banco

No arquivo `php/conexao.php`, o modo é controlado por uma constante semelhante a:

```php
define('MODO_BANCO', 'simples');
```

Use `simples` para a estrutura enxuta e `robusto` para a estrutura completa.

## E-mail do sistema
O envio de e-mails do sistema está centralizado em `php/conexao.php`.

O sistema usa uma chave de API e um remetente configurados via variáveis de ambiente (veja a seção de configuração acima). Caso o provedor de e-mail/transmissão não esteja configurado, o sistema tenta um fallback para envio local (função `mail()`), mas esse envio local pode não funcionar em ambientes de desenvolvimento sem um servidor SMTP configurado.

Para que o envio de e-mail funcione corretamente, entre em contato com os desenvolvedores para que passem as variáveis de configuração necessárias (por exemplo: API key do provedor e e-mail remetente) e realizem a configuração no servidor.

## Contas de teste

Existem contas e dados de exemplo nos scripts em `database/` para facilitar testes locais. Esses dados são apenas ilustrativos — não use essas credenciais em ambiente de produção. Recomenda-se alterar as senhas de exemplo após importar os dumps.

## Regras e validações

- O nome não aceita números.
- O e-mail precisa ser válido.
- CPF e telefone têm validação e máscara.
- O cadastro exige confirmação de senha e aceite dos termos.
- Novos cadastros entram como `pendente` até aprovação do admin.
- A sessão expira por inatividade após 1 hora.
- O perfil do usuário não permite alterações diretas em campos sensíveis.

## Documentação complementar

- `docs/regras_de_negocio_requisitos.md` — regras e requisitos do sistema.
- `docs/dicionario_de_dados.md` — descrição dos campos do banco.
- `docs/diagrama_relacional.mmd` — diagrama do relacionamento entre tabelas.
- `docs/respostas_estrutura_de_dados.md` — respostas sobre a modelagem e estrutura.

## Observações finais

- Os arquivos de vídeo devem ficar em `videos/` durante o desenvolvimento.
- O painel admin e o painel do fiel usam includes e links relativos, então qualquer mudança de pasta deve ser revisada com cuidado.
- Antes de trocar o modo do banco ou mexer na estrutura SQL, confira os arquivos em `database/` e as consultas condicionais em `php/conexao.php`.
