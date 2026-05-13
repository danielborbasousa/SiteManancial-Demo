# Regras de Negócio e Requisitos

## Regras de Negócio
- O usuário deve conseguir navegar entre landing page, login, cadastro e voltar para a landing em qualquer ponto do fluxo público.
- O cadastro exige nome, e-mail, telefone, CPF, filial, função, endereço, senha e confirmação de senha.
- O nome não pode conter números.
- O e-mail deve ser válido e não pode ser cadastrado em duplicidade.
- O CPF deve ser único e ter 11 dígitos.
- O telefone deve ter 10 ou 11 dígitos e ser exibido com máscara.
- A senha deve ter no mínimo 6 caracteres.
- O usuário precisa aceitar os termos de uso antes de criar a conta.
- O perfil do usuário é somente leitura.
- A sessão deve expirar após 1 hora de inatividade.
- O admin pode criar, listar, editar e excluir conteúdos em vídeo.
- O admin deve conseguir visualizar cursos e progresso associado a usuários.
- Os vídeos locais devem estar na pasta `videos/`.

## Requisitos Funcionais
- RF01: Exibir landing page pública com acesso a login e cadastro.
- RF02: Permitir autenticação de usuários e administradores.
- RF03: Permitir criação de conta com validações de campos.
- RF04: Permitir exibição de vídeos cadastrados e do vídeo de demonstração.
- RF05: Permitir o admin cadastrar, editar e excluir vídeos.
- RF06: Permitir visualizar cursos e progresso dos usuários.
- RF07: Permitir consulta de perfil do usuário em modo somente leitura.
- RF08: Permitir busca de conteúdos cadastrados.
- RF09: Permitir contato com a instituição a partir da área autenticada.
- RF10: Encerrar a sessão por inatividade.

## Requisitos Não Funcionais
- RNF01: O sistema deve ser responsivo em desktop e mobile.
- RNF02: As telas devem usar navegação consistente e sem links quebrados.
- RNF03: O sistema deve validar os principais campos digitáveis no cliente e no servidor.
- RNF04: O acesso autenticado deve usar sessão com token e tempo de expiração.
- RNF05: O código deve respeitar a separação entre interface, lógica e banco.
- RNF06: O conteúdo visual deve permanecer leve para uso em laboratório/aula.
- RNF07: O sistema deve funcionar com `MODO_BANCO` simples e robusto.




na landingpage deixe os campos de home, cursos e sobre na mesma reta dos demais, todos no topo e melhore o css do logine criar conta..

Melhore o texto abaixo da senha, coloque banco para o usuário ler.

No projeto inteiro, desde a landingpage a todas as demais telas, coloque um tema claro e escuro para o usuário escolher e as demais telas devem seguir a opção, então se o usuário trocar na landingpage as demais devem seguir e o inverso tbm.