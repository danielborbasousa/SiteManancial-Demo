# Dicionário de Dados — SiteManancial-Demo

Este documento descreve as tabelas principais, campos e relacionamentos usados no projeto (baseado em `Banco Igreja Robusto.sql`).

---

## ID_FILIAL
- PK: `IDL_ID` (INT)
- Campos:
  - `IDL_NOME` VARCHAR(120) — nome da filial (UNIQUE)
  - `IDL_CIDADE` VARCHAR(100)
  - `IDL_ESTADO` CHAR(2)
  - `IDL_ATIVA` TINYINT(1) — indicador ativo
  - `IDL_CRIADO_EM` TIMESTAMP
  - `IDL_ATUALIZADO_EM` TIMESTAMP
- Observações: representa unidades/filiais da igreja.

---

## ID_FIEL
- PK: `IDF_ID` (INT)
- Campos:
  - `IDF_NOME` VARCHAR(100) — nome completo
  - `IDF_TELEFONE` VARCHAR(20)
  - `IDF_EMAIL` VARCHAR(150) — email único (UK_ID_FIEL_EMAIL)
  - `IDF_CPF` CHAR(11) — CPF único (UK_ID_FIEL_CPF)
  - `IDF_FILIAL_ID` INT — FK -> `ID_FILIAL(IDL_ID)`
  - `IDF_FUNCAO` VARCHAR(50)
  - `IDF_ENDERECO` VARCHAR(200)
  - `IDF_SENHA_HASH` VARCHAR(255) — hash da senha (`password_hash` no PHP)
  - `IDF_ATIVO` TINYINT(1)
  - `IDF_EMAIL_VERIFICADO_EM` DATETIME
  - `IDF_ULTIMO_LOGIN_EM` DATETIME
  - `IDF_CRIADO_EM`, `IDF_ATUALIZADO_EM`, `IDF_REMOVIDO_EM`
- Observações: identidade do usuário; não armazenar senhas em texto puro.

---

## ID_ADMIN
- PK: `IDA_ID` (INT)
- Campos:
  - `IDA_FIEL_ID` INT — FK -> `ID_FIEL(IDF_ID)` (representa qual fiel é admin)
  - `IDA_NIVEL` ENUM('SUPER','GESTOR','EDITOR')
  - `IDA_ATIVO` TINYINT(1)
- Observações: tabela de privilegios administrativos.

---

## ID_CURSO
- PK: `IDC_ID` (INT)
- Campos:
  - `IDC_TITULO` VARCHAR(180)
  - `IDC_DESCRICAO` TEXT
  - `IDC_CAPA_URL` VARCHAR(500)
  - `IDC_CARGA_HORARIA_MIN` INT
  - `IDC_STATUS` ENUM('RASCUNHO','PUBLICADO','ARQUIVADO')
  - `IDC_ADMIN_CRIADOR_ID` INT — FK -> `ID_ADMIN(IDA_ID)`
  - `IDC_CRIADO_EM`, `IDC_ATUALIZADO_EM`
- Observações: representa cursos/coleções de conteúdo.

---

## ID_MODULO
- PK: `IDM_ID` (INT)
- Campos:
  - `IDC_ID` INT — FK -> `ID_CURSO(IDC_ID)`
  - `IDM_TITULO` VARCHAR(180)
  - `IDM_DESCRICAO` TEXT
  - `IDM_ORDEM` SMALLINT — ordem do módulo
- Observações: organiza conteúdo dentro do curso.

---

## ID_CONTENT
- PK: `IDCT_ID` (BIGINT)
- Campos:
  - `IDC_ID` INT — FK -> `ID_CURSO(IDC_ID)`
  - `IDM_ID` INT — FK opcional -> `ID_MODULO(IDM_ID)`
  - `IDCT_TIPO` ENUM('VIDEO','DOCUMENTO','IMAGEM','AUDIO','LINK')
  - `IDCT_TITULO` VARCHAR(180)
  - `IDCT_DESCRICAO` TEXT
  - `IDCT_URL` VARCHAR(500) — URL local ou externo
  - `IDCT_DURACAO_SEGUNDOS` INT
  - `IDCT_ORDEM` SMALLINT
  - `IDCT_PUBLICADO` TINYINT(1)
- Observações: conteúdo consumível. Para vídeos locais use caminhos relativos dentro de `videos/`.

---

## ID_MATRICULA
- PK: `IDMATR_ID` (BIGINT)
- Campos:
  - `IDF_ID` INT — FK -> `ID_FIEL(IDF_ID)`
  - `IDC_ID` INT — FK -> `ID_CURSO(IDC_ID)`
  - `IDMATR_STATUS` ENUM('ATIVA','PAUSADA','CONCLUIDA','CANCELADA')
  - `IDMATR_PERCENTUAL` DECIMAL(5,2)
- Observações: evita duplicidade com UNIQUE (IDF_ID, IDC_ID).

---

## ID_PROGRESSO
- PK: `IDPR_ID` (BIGINT)
- Campos:
  - `IDMATR_ID` BIGINT — FK -> `ID_MATRICULA(IDMATR_ID)`
  - `IDCT_ID` BIGINT — FK -> `ID_CONTENT(IDCT_ID)`
  - `IDPR_STATUS` ENUM('NAO_INICIADO','EM_ANDAMENTO','CONCLUIDO')
  - `IDPR_PERCENTUAL` DECIMAL(5,2)
  - `IDPR_TEMPO_ASSISTIDO_SEGUNDOS` INT
- Observações: registra progresso por conteúdo para cada matrícula.

---

## ID_SESSAO
- PK: `IDS_ID` (BIGINT)
- Campos:
  - `IDF_ID` INT — FK -> `ID_FIEL(IDF_ID)`
  - `IDS_TOKEN` CHAR(64) — token de sessao (gerado seguro)
  - `IDS_IP`, `IDS_USER_AGENT`, `IDS_EXPIRA_EM`, `IDS_REVOGADA`
- Observações: modelo para controlar sessões persistentes e revogacao.

---

## ID_RECUPERACAO_SENHA
- PK: `IDR_ID` (BIGINT)
- Campos:
  - `IDF_ID` INT — FK -> `ID_FIEL(IDF_ID)`
  - `IDR_TOKEN` CHAR(64)
  - `IDR_EXPIRA_EM` DATETIME
  - `IDR_USADO_EM` DATETIME NULL
- Observações: tokens de recuperação, expiram em curto periodo.

---

## Observações gerais
- Charset recomendado: `utf8mb4` para suportar emojis/acentos.
- Senhas devem ser armazenadas com `password_hash()` e verificadas com `password_verify()`.
- Use `prepared statements` (mysqli_stmt ou PDO) para todas as consultas que recebem input do usuário.
- Indices adicionais são recomendados para colunas usadas em filtros (ex.: `IDCT_TIPO`, `IDF_EMAIL`, `IDC_TITULO`).

---

Fim do dicionário de dados.
