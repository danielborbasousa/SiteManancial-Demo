# Requisitos Funcionais e Não Funcionais

# SiteManancial-Demo

Sistema web desenvolvido para a Missão Evangélica Manancial da Esperança, com foco em gerenciamento de usuários, controle administrativo, autenticação, aprovação de fiéis e disponibilização de conteúdos multimídia.

O sistema foi desenvolvido utilizando apenas:

* PHP
* HTML
* CSS
* JavaScript
* MySQL

conforme exigência acadêmica da disciplina.

---

# 1. Objetivo do Sistema

O sistema tem como objetivo fornecer uma plataforma interna para a igreja, permitindo:

* Cadastro de fiéis
* Controle de acesso administrativo
* Aprovação manual de usuários
* Disponibilização de conteúdos multimídia
* Gerenciamento de usuários
* Organização de conteúdos
* Controle de permissões administrativas

O sistema possui dois perfis principais:

* FIEL
* SUPER ADMIN

---

# 2. Fluxo Principal do Sistema

1. O visitante acessa a página inicial.
2. O usuário realiza o cadastro.
3. O sistema registra o cadastro com status `pendente`.
4. O SUPER ADMIN visualiza a fila de solicitações.
5. O SUPER ADMIN valida os dados do fiel com base nos registros da igreja.
6. O SUPER ADMIN aprova ou nega o acesso.
7. O sistema envia uma notificação ao usuário.
8. Após aprovação, o fiel consegue acessar o sistema.

---

# 3. Requisitos Funcionais

## RF01 - Cadastro de usuários

O sistema deve permitir cadastro contendo:

* Nome completo
* E-mail
* CPF
* Telefone
* Senha
* Confirmação de senha

---

## RF02 - Validação de cadastro

O sistema deve validar:

* Nome sem números
* E-mail válido
* CPF válido
* Telefone válido
* Confirmação de senha

---

## RF03 - Login de usuários

O sistema deve permitir autenticação utilizando:

* E-mail
* Senha

---

## RF04 - Controle de status do usuário

O sistema deve possuir os status:

* Pendente
* Aprovado
* Negado

---

## RF05 - Aprovação manual de usuários

O SUPER ADMIN deve aprovar manualmente os novos usuários cadastrados.

---

## RF06 - Fila de solicitações

O sistema deve possuir uma fila de solicitações pendentes para análise administrativa.

---

## RF07 - Validação de fiéis

O SUPER ADMIN deve poder comparar os dados cadastrados com os registros da igreja antes da aprovação.

---

## RF08 - Bloqueio de login

Usuários pendentes ou negados não devem conseguir acessar o sistema.

---

## RF09 - Notificação de solicitação recebida

Após o cadastro, o sistema deve informar:

"Sua solicitação foi recebida e está aguardando aprovação."

---

## RF10 - Notificação de aprovação

Após aprovação, o sistema deve informar:

"Seu acesso foi aprovado."

---

## RF11 - Notificação de recusa

Após recusa, o sistema deve informar:

"Seu cadastro não foi aprovado."

---

## RF12 - Envio de e-mail

O sistema deve permitir envio de mensagens por e-mail relacionadas a:

* Aprovação
* Recusa
* Avisos administrativos

---

## RF13 - Dashboard do fiel

O fiel deve possuir acesso a:

* Página inicial autenticada
* Perfil
* Conteúdos
* Notificações
* Busca de conteúdos

---

## RF14 - Perfil simplificado do fiel

A área do fiel deve ser mais simples e focada apenas em consumo de conteúdo.

---

## RF15 - Perfil somente leitura

O fiel poderá visualizar seus dados, mas não editá-los diretamente.

---

## RF16 - Painel administrativo

O SUPER ADMIN deve possuir acesso a:

* Aprovação de usuários
* Gerenciamento de conteúdos
* Controle de usuários
* Controle de permissões
* Organização de mídias

---

## RF17 - Gerenciamento de usuários

O SUPER ADMIN deve poder:

* Visualizar usuários
* Editar usuários
* Ativar usuários
* Desativar usuários
* Bloquear usuários

---

## RF18 - Alteração de perfil pelo SUPER ADMIN

O SUPER ADMIN deve poder corrigir:

* Nome
* E-mail
* CPF
* Telefone
* Filial
* Função
* Status

---

## RF19 - Controle de permissões

O sistema deve possuir controle de permissões administrativas.

---

## RF20 - Promoção de usuário

O SUPER ADMIN deve poder promover um fiel para SUPER ADMIN.

---

## RF21 - Remoção de administrador

O SUPER ADMIN deve poder remover permissões administrativas, retornando o usuário para o perfil de fiel.

---

## RF22 - Níveis de acesso

O sistema deve possuir apenas dois níveis de acesso:

* SUPER ADMIN
* FIEL

---

## RF23 - Gerenciamento de conteúdos

O SUPER ADMIN deve poder:

* Adicionar conteúdos
* Editar conteúdos
* Excluir conteúdos
* Publicar conteúdos
* Ocultar conteúdos

---

## RF24 - Gerenciamento multimídia

O sistema deve permitir gerenciamento de:

* Vídeos
* Imagens
* Áudios
* PDFs
* Documentos

---

## RF25 - Tipagem de conteúdo

Os conteúdos devem possuir tipos como:

* VIDEO
* IMAGEM
* AUDIO
* DOCUMENTO
* PDF

---

## RF26 - Biblioteca de conteúdos

O sistema deve possuir listagem organizada de conteúdos.

---

## RF27 - Busca de conteúdos

O sistema deve permitir pesquisa por:

* Nome
* Categoria
* Tipo de mídia

---

## RF28 - Organização por categorias

Os conteúdos devem poder ser organizados em categorias.

---

## RF29 - Reprodução de mídia

O sistema deve permitir reprodução e visualização de conteúdos multimídia.

---

## RF30 - Armazenamento local de arquivos

Os arquivos devem ser armazenados localmente em diretórios organizados.

---

## RF31 - Tema claro e escuro

O sistema deve permitir alternância de tema:

* Claro
* Escuro

---

## RF32 - Persistência de tema

A preferência de tema deve permanecer salva no navegador.

---

## RF33 - Controle de sessão

O sistema deve:

* Criar sessão após login
* Encerrar sessão após logout
* Expirar sessão após inatividade

---

## RF34 - Expiração de sessão

A sessão deve expirar após 1 hora sem atividade.

---

## RF35 - Redirecionamento por perfil

O sistema deve redirecionar:

* SUPER ADMIN → Painel administrativo
* FIEL → Dashboard

---

## RF36 - Compatibilidade com dois bancos

O sistema deve funcionar com:

* Banco simples
* Banco robusto

---

## RF37 - Vídeo padrão

O sistema deve exibir vídeo de demonstração caso não existam conteúdos cadastrados.

---

## RF38 - Sistema de notificações

O sistema deve exibir notificações sobre:

* Aprovações
* Recusas
* Atualizações
* Novos conteúdos

---

## RF39 - Controle de conteúdos publicados

O SUPER ADMIN deve poder controlar conteúdos ativos ou ocultos.

---

## RF40 - Estrutura escalável

O sistema deve permitir futuras implementações como:

* Cursos
* Certificados
* Uploads
* Gamificação
* Aplicativo mobile
* Área de comentários
* Logs administrativos

---

# 4. Requisitos Não Funcionais

## RNF01 - Tecnologias obrigatórias

O sistema deve utilizar:

* PHP
* HTML
* CSS
* JavaScript
* MySQL

---

## RNF02 - Ambiente acadêmico

O sistema deve funcionar em ambiente local utilizando:

* XAMPP
* Apache
* MySQL

---

## RNF03 - Segurança

O sistema deve possuir armazenamento seguro de senhas.

---

## RNF04 - Controle de acesso

Rotas administrativas devem ser acessíveis apenas para SUPER ADMIN autenticado.

---

## RNF05 - Responsividade

O sistema deve funcionar em:

* Desktop
* Tablet
* Smartphone

---

## RNF06 - Compatibilidade de navegadores

O sistema deve funcionar em:

* Google Chrome
* Edge
* Firefox

---

## RNF07 - Organização do projeto

O projeto deve possuir separação organizada entre:

* Arquivos PHP
* CSS
* JavaScript
* Banco de dados
* Documentação

---

## RNF08 - Facilidade de manutenção

O código deve possuir:

* Organização
* Estrutura simples
* Comentários essenciais

---

## RNF09 - Integridade dos dados

O banco robusto deve utilizar:

* Chaves estrangeiras
* Restrições
* Índices

---

## RNF10 - Escalabilidade

O sistema deve permitir crescimento futuro sem necessidade de reconstrução total.

---

## RNF11 - Simplicidade de uso

A interface deve ser simples para usuários com pouco conhecimento técnico.

---

## RNF12 - Organização de mídia

Os arquivos multimídia devem permanecer organizados em diretórios específicos.

---

## RNF13 - Compatibilidade multimídia

O sistema deve suportar formatos comuns de:

* Vídeo
* Imagem
* Áudio
* Documento

---

## RNF14 - Disponibilidade

O sistema deve permanecer disponível durante o uso dos usuários autenticados.

---

## RNF15 - Estrutura preparada para auditoria

O sistema deve permitir futura implementação de:

* Logs
* Histórico de ações
* Monitoramento administrativo

---

## RNF16 - Escalabilidade administrativa

A estrutura administrativa deve permitir criação futura de novas permissões e funções.

---

## RNF17 - Simplicidade acadêmica

O sistema deve manter estrutura simples e compatível com os requisitos da disciplina.

---

# 5. Possíveis Melhorias Futuras

* Sistema de cursos completos
* Certificados automáticos
* Upload de arquivos
* Comentários em conteúdos
* Área de eventos
* Gamificação
* Ranking de usuários
* Aplicativo mobile
* Integração com banco robusto completo
* Sistema de logs
* Painel analítico administrativo
* Recuperação de senha por e-mail
* Upload de imagens de perfil
* API futura para integração externa
