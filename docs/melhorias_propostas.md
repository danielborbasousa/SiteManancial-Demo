# Melhorias Propostas para o Projeto

Este documento lista melhorias simples, pensando em um projeto de nivel iniciante, com implementacao direta e sem excessos.

## 1) Padronizar os nomes das rotas
- Usar `login.php` como pagina oficial de acesso.
- Manter `index.php` apenas como redirecionamento.
- Revisar links antigos que ainda apontam para arquivos antigos.

## 2) Organizar melhor o painel do admin
- Criar uma pagina unica para o administrador gerenciar conteudos.
- Exibir opcoes de enviar, listar, visualizar e excluir videos.
- Mostrar o link de admin apenas para usuarios com perfil administrativo.

## 3) Melhorar a validacao dos formularios
- Conferir se os campos obrigatorios foram preenchidos.
- Revisar formatos de email, cpf, telefone e senha.
- Exibir mensagens mais claras quando houver erro.

## 4) Melhorar a seguranca basica
- Usar `session_regenerate_id(true)` no login.
- Proteger paginas internas com verificacao de sessao.
- Evitar mostrar erros detalhados do banco para o usuario final.

## 5) Padronizar o banco de dados
- Trabalhar com um unico schema principal.
- Garantir que os campos essenciais tenham `NOT NULL`.
- Usar `UNIQUE` em email e CPF.
- Manter `FOREIGN KEY` nas relacoes principais.

## 6) Melhorar o cadastro de conteudo
- Permitir apenas arquivos de video validos.
- Salvar os videos em uma pasta organizada.
- Remover do banco e do disco quando o admin excluir um video.

## 7) Ajustar a experiencia do usuario
- Mostrar mensagem quando nao houver conteudo cadastrado.
- Manter textos padronizados na linguagem do projeto.
- Melhorar a navegacao entre login, dashboard e admin.

## 8) Evolucoes futuras simples
- Criar busca com melhor filtragem.
- Inserir pagina de edicao de conteudo.
- Adicionar contagem de videos publicados.
- Criar relatorio basico de matriculas e progresso.

## Prioridade sugerida
1. Login padronizado.
2. CRUD do admin.
3. Validacao e seguranca basica.
4. Padronizacao do banco.
5. Melhorias visuais e de navegacao.
