# Sobre o projeto

## Visao geral
O SiteManancial-Demo e um sistema web para centralizar conteudos da igreja (videos, estudos e informacoes institucionais) em uma interface unica para membros e visitantes. A aplicacao usa arquitetura monolitica simples: o PHP renderiza as paginas no servidor, consulta o MySQL e entrega HTML pronto ao navegador.

Objetivo principal:
- facilitar acesso aos materiais da igreja;
- manter autenticacao para area interna;
- oferecer base academica e pratica para evolucao futura.

## Front-end
### Stack utilizada
- HTML5 para estrutura das paginas.
- CSS3 para identidade visual e responsividade.
- Bootstrap 5 via CDN para grid, componentes e utilitarios.
- JavaScript puro para pequenas interacoes de interface.

### Organizacao de arquivos
- css/styles.css: tema global, tipografia, componentes de autenticacao e cards.
- js/script.js: scroll horizontal dos carrosseis e leitura de icones.
- assets/logo.png e assets/icons.json: identidade visual e recursos de apoio.

### Paginas e experiencia do usuario
- Login e cadastro com layout padronizado.
- Dashboard com area de destaque e mini player para videos.
- Paginas internas de apoio: perfil, contato, sobre e notificacoes.
- Navegacao superior com links para os modulos principais.

### Comportamentos implementados
- Carrosseis horizontais com rolagem por mouse.
- Atualizacao de icones no carregamento da pagina.
- Mascaras de CPF e telefone no cadastro.
- Mini player no dashboard com troca de video ao clicar nos cards.

### Pontos de atencao no front-end
- Existem arquivos legados em paralelo (exemplo: paginas estaticas) que podem causar duplicidade de fluxo.
- Alguns caminhos de recursos dependem da pasta atual e podem falhar quando a rota muda.

## Back-end
### Stack e estilo arquitetural
- PHP procedural.
- Sessao nativa do PHP para controle de autenticacao.
- MySQLi para conexao e consultas ao banco.

### Estrutura de backend por responsabilidade
- index.php (raiz): redirecionamento para entrada da aplicacao.
- php/conexao.php: configuracao de conexao com MySQL.
- php/index.php: login e criacao da sessao.
- php/register.php: validacao de campos e cadastro de usuario.
- php/dashboardusuario.php: montagem do painel e listagem de conteudos.
- php/assistir_video.php: player dedicado por conteudo.
- php/perfil.php: consulta e exibicao dos dados do usuario logado.
- php/busca.php: busca de conteudo por titulo/descricao.
- php/contato.php: formulario de contato via endpoint externo.
- php/sair.php: encerramento da sessao.

### Fluxo de autenticacao atual
1. Usuario envia email e senha no formulario de login.
2. O backend consulta a tabela de usuarios.
3. Em caso de sucesso, cria variavel de sessao Usuario_logado.
4. Paginas internas verificam a sessao antes de renderizar conteudo.
5. Logout remove variaveis de sessao e redireciona para login.

### Fluxo de conteudo
1. Dashboard consulta ID_CONTENT filtrando tipo video.
2. Resultados validos sao exibidos como cards no feed.
3. O primeiro item vira video inicial do mini player.
4. Clique no card atualiza o mini player.
5. Clique em abrir video redireciona para pagina de reproducao.

### Validacoes implementadas no backend
- tamanho e formato basico de nome, email, telefone e cpf no cadastro;
- exigencia de sessao para paginas protegidas;
- tentativa de restringir videos para pasta local.

### Limites atuais da camada backend
- uso de SQL dinamico em pontos sensiveis;
- senha ainda sem hash na estrutura basica em uso;
- necessidade de padronizar caminhos de arquivo e regras de seguranca;
- script de setup de video deve ser protegido por autenticacao/permissao.

## Banco de dados
### Arquivos de referencia no projeto
- database/Banco Igreja.txt: schema simples usado para demonstracao.
- database/Banco Igreja Robusto.sql: schema evoluido com constraints e governanca melhores.

### Modelo simples (em uso para rodar rapido)
Caracteristicas:
- tabelas principais de usuarios, cursos, modulos, conteudos, matriculas e progresso;
- baixa rigidez de integridade referencial;
- rapido para demonstracao local.

Uso recomendado:
- provas de conceito;
- validacao de fluxo funcional;
- ambiente de sala/laboratorio.

### Modelo robusto (evolucao recomendada)
Caracteristicas:
- padrao utf8mb4, chaves estrangeiras e indices dedicados;
- separacao clara de perfis, filiais, sessoes e recuperacao de senha;
- campos de auditoria e status para gestao de ciclo de vida dos dados;
- estrutura preparada para crescimento.

Uso recomendado:
- homologacao e producao;
- controle de consistencia entre tabelas;
- base para recursos de administracao e relatorios.

### Entidades centrais do dominio
- ID_FIEL: cadastro de usuarios e dados de acesso.
- ID_ADMIN: usuarios com privilegio de administracao.
- ID_CURSO: cursos cadastrados na plataforma.
- ID_MODULO: organizacao interna dos cursos.
- ID_CONTENT: itens consumiveis (video, documento, imagem, audio, link).
- ID_MATRICULA: vinculo usuario x curso.
- ID_PROGRESSO: acompanhamento por conteudo.
- ID_SESSAO: controle de sessao e validade de acesso.
- ID_FILIAL: organizacao das unidades da igreja.

### Integracao real entre aplicacao e banco
- formularios enviam dados para scripts PHP;
- scripts validam e executam consultas/inserts;
- resultados retornam para renderizacao no HTML;
- sessao define quais consultas e paginas o usuario pode acessar.

## Relacao entre front, back e banco
O front-end funciona como camada de apresentacao e captura de dados. O back-end processa regras de negocio e autorizacao. O banco persiste usuarios, conteudos e progresso. Em termos praticos:

1. Front coleta dados do usuario.
2. Back valida e consulta/grava no MySQL.
3. Banco retorna dados estruturados.
4. Back monta a resposta HTML.
5. Front exibe o resultado ao usuario final.

## Estado atual e maturidade tecnica
### O que ja esta bem encaminhado
- separacao de pastas por tipo de recurso;
- fluxo principal de autenticacao e painel funcionando;
- material de banco robusto ja produzido para migracao;
- documentacao inicial de setup e estrutura.

### O que deve ser priorizado na proxima fase
- seguranca: prepared statements e hash de senha;
- padronizacao de rotas e remocao de paginas legadas duplicadas;
- alinhamento definitivo entre codigo PHP e schema robusto;
- revisao de tratamento de erro para nao expor detalhes internos.

## Conclusao
O projeto possui base funcional solida para estudo e demonstracao, com potencial de evolucao para um sistema institucional mais robusto. O caminho tecnico recomendado e manter a experiencia atual do usuario, enquanto fortalece seguranca, consistencia de dados e padronizacao arquitetural em ciclos de melhoria controlados.