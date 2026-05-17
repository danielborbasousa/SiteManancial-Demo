# Matriz de Requisitos — Status

Requisito | Status | Observações
--- | --- | ---
RF01 | Presente | Cadastro com nome, e‑mail, CPF, telefone, senha e confirmação (`php/fiel/register.php`).
RF02 | Presente | Validações cliente/servidor (nome sem números, e‑mail, CPF, telefone, confirmação de senha).
RF03 | Presente | Login por e‑mail e senha (`login.php`).
RF04 | Presente | Status `pendente`/`aprovado`/`negado` no banco e fluxo.
RF05 | Presente | Aprovação manual via admin (`php/admin/admin_aprovar_usuarios.php`).
RF06 | Presente | Fila de solicitações visível no admin.
RF07 | Parcial | Admin visualiza dados para validação; sem integração automática com registros da igreja.
RF08 | Presente | Bloqueio de login para `pendente` e `negado` implementado.
RF09 | Ausente | Não há mensagem automática clara após cadastro com o texto exigido.
RF10 | Presente | Notificação de aprovação criada em tabela de notificações.
RF11 | Presente | Notificação de recusa criada com motivo possível.
RF12 | Ausente | Não foram encontradas rotinas de envio de e‑mail (SMTP/`mail()`).
RF13 | Presente | Dashboard do fiel existe (`php/fiel/`).
RF14 | Presente | Perfil do fiel simplificado (somente leitura).
RF15 | Presente | Perfil somente leitura (usuário não edita diretamente).
RF16 | Presente | Painel administrativo implementado (`php/admin/`).
RF17 | Presente | Gerenciamento de usuários (listar/editar/ativar/desativar/bloquear) presente.
RF18 | Presente | SUPER ADMIN pode alterar perfil/atributos via admin.
RF19 | Presente | Controle de permissões administrativos implementado.
RF20 | Presente | Promoção a administrador suportada (modais de promoção/removal).
RF21 | Presente | Remoção de permissões implementada.
RF22 | Presente | Dois níveis de acesso suportados (admin/fiel).
RF23 | Parcial | Gerenciamento de conteúdos existe; confirmar publicar/ocultar em `admin_conteudos.php`.
RF24 | Presente | Multimídia suportada; pasta `videos/` e tabelas de conteúdo.
RF25 | Presente | Tipagem de conteúdo (ex.: `video`) usada nas consultas.
RF26 | Presente | Biblioteca/listagem de conteúdos implementada.
RF27 | Presente | Busca de conteúdos existe (`php/fiel/busca.php`).
RF28 | Parcial | Categoria/organização há indícios; uso completo a confirmar em admin_conteudos.
RF29 | Presente | Reprodução/visualização de mídia suportada (player/views).
RF30 | Presente | Armazenamento local de arquivos na pasta `videos/`.
RF31 | Presente | Tema claro/escuro com toggle (`js/theme.js`).
RF32 | Presente | Persistência do tema em `localStorage`.
RF33 | Presente | Controle de sessão com token e registro em BD (`php/conexao.php`).
RF34 | Presente | Expiração de sessão ~1 hora (IDS_EXPIRA_EM / constante de sessão).
RF35 | Presente | Redirecionamento por perfil após login (admin → painel, fiel → dashboard).
RF36 | Presente | Suporta `MODO_BANCO` simples e robusto (`php/conexao.php`).
RF37 | Parcial | Mensagem/placeholder quando não há vídeos; não há lógica explícita de "vídeo de demonstração" automático.
RF38 | Presente | Sistema de notificações persistente (`ID_NOTIFICACAO`, página de notificações).
RF39 | Parcial | Controle de conteúdos publicados/ocultos provável; ver `admin_conteudos.php` para confirmação.
RF40 | Presente | Estrutura com tabelas para cursos/matrículas e expansão futura.

---

Requisito Não Funcional | Status | Observações
--- | --- | ---
RNF01 | Presente | Tecnologias obrigatórias usadas (PHP/HTML/CSS/JS/MySQL).
RNF02 | Presente | Documentação e README indicam XAMPP/Apache/MySQL.
RNF03 | Parcial | Em `robusto` usa `password_hash`; em `simples` senhas podem ficar em texto (risco).
RNF04 | Presente | Rotas admin protegidas por `auth_require()` e checagem de roles.
RNF05 | Presente | Uso de Bootstrap e layouts responsivos.
RNF06 | Presente | Código compatível com navegadores modernos (implícito).
RNF07 | Presente | Organização por pastas (`php/`, `css/`, `js/`, `videos/`).
RNF08 | Parcial | Estrutura razoável; há áreas com lógica repetida que podem ser refatoradas.
RNF09 | Presente | Banco robusto disponível com chaves e restrições (`database/`).
RNF10 | Presente | Arquitetura modular favorece escalabilidade futura.
RNF11 | Presente | Interface simples e direta para usuários finais.
RNF12 | Presente | Mídia organizada em diretórios específicos.
RNF13 | Presente | Suporte a formatos multimídia comuns.
RNF14 | Parcial | Disponibilidade local; não há mecanismos de alta disponibilidade/monitoramento.
RNF15 | Parcial | Sessões registradas; faltam logs administrativos detalhados.
RNF16 | Presente | Permissões e níveis existem permitindo expansão.
RNF17 | Presente | Projeto mantém simplicidade adequada para uso acadêmico.

---

Observação final: entradas marcadas como "Parcial" ou "Ausente" podem ser detalhadas se desejar; posso gerar links diretos aos trechos de código como evidência.
