# Respostas da Matéria de Estrutura de Dados

## 1) Do que se trata? (modelo de negocio)
O projeto SiteManancial-Demo e uma plataforma de conteudo educacional e institucional para igreja. O modelo de negocio e de organizacao e distribuicao de conteudos (videos, documentos e trilhas de estudo), com autenticacao de usuarios e acompanhamento de progresso.

Em termos de dominio:
- usuarios (fieis/alunos) acessam conteudos;
- administradores organizam cursos e modulos;
- o sistema registra matriculas e evolucao de consumo.

## 2) Para que isso vai ajudar?
O sistema ajuda a centralizar informacoes que antes estariam dispersas (grupos de mensagem, planilhas, arquivos soltos). Com isso:
- melhora o acesso ao material de estudo;
- padroniza o processo de aprendizagem;
- facilita acompanhamento de quem iniciou/concluiu conteudos;
- reduz trabalho manual da equipe administrativa.

## 3) Como pode beneficiar outras pessoas?
Beneficios diretos e indiretos:
- membros da igreja: acesso facil e organizado ao conteudo;
- liderancas: visao de progresso e matriculas;
- visitantes: porta de entrada para conhecer cursos e mensagens;
- outras instituicoes (escolas, projetos sociais, ONGs): o mesmo modelo pode ser reutilizado para trilhas de ensino.

## 4) Como foram feitas as consultas?
No projeto atual (PHP + MySQLi), as consultas sao feitas no backend em arquivos da pasta php.

Exemplos práticos:
- login: busca usuario por email/senha em ID_FIEL;
- dashboard: lista conteudos da tabela ID_CONTENT filtrando por tipo video;
- perfil: busca os dados do usuario logado por email;
- busca: filtra titulo/descricao de conteudo.

Exemplo de consulta de conteudo:

```sql
SELECT *
FROM ID_CONTENT
WHERE IDCT_TIPO = 'video'
ORDER BY IDCT_ORDEM;
```

Exemplo conceitual de busca textual:

```sql
SELECT *
FROM ID_CONTENT
WHERE IDCT_TITULO LIKE '%termo%'
   OR IDCT_DESCRICAO LIKE '%termo%';
```

Observacao tecnica importante:
- Em producao, o ideal e usar prepared statements para evitar SQL injection.

## 5) Quais tabelas foram usadas? (explicar)
Base principal do dominio (modelo robusto):

1. ID_FILIAL
- Armazena as unidades da instituicao.
- Relaciona localmente os usuarios.

2. ID_FIEL
- Cadastro dos usuarios da plataforma.
- Dados pessoais e credenciais (hash de senha no modelo robusto).

3. ID_ADMIN
- Define quais usuarios possuem permissao administrativa.
- Niveis de acesso (SUPER, GESTOR, EDITOR).

4. ID_CURSO
- Estrutura dos cursos publicados no sistema.
- Relaciona com o admin que criou.

5. ID_MODULO
- Segmenta o curso em blocos de estudo.
- Controla ordem de exibicao.

6. ID_CONTENT
- Conteudos concretos (video, documento, imagem, audio, link).
- Conectado ao curso e opcionalmente ao modulo.

7. ID_MATRICULA
- Relacao entre usuario e curso.
- Status da jornada (ativa, pausada, concluida, cancelada).

8. ID_PROGRESSO
- Detalha progresso por conteudo de cada matricula.
- Permite medir percentual e tempo assistido.

9. ID_SESSAO
- Controle de sessoes e expiracao de acesso.

10. ID_RECUPERACAO_SENHA
- Fluxo de redefinicao de senha com token e validade.

## 6) Quais restricoes necessarias?
As principais restricoes de qualidade e integridade:

- unicidade:
  - email e cpf do usuario nao podem repetir;
- obrigatoriedade:
  - campos essenciais com NOT NULL (nome, email, senha hash, titulos);
- integridade referencial:
  - uso de FOREIGN KEY entre curso, modulo, conteudo, matricula e progresso;
- dominio de valores:
  - status controlados por ENUM (ex.: PUBLICADO, ATIVA, CONCLUIDO);
- consistencia numerica:
  - percentuais entre 0 e 100 (CHECK);
- seguranca:
  - senha com hash e consultas preparadas.

## 7) Como foi pensado nas chaves primarias, secundarias e relacoes?
Modelagem adotada:

- Chaves primarias (PK):
  - IDs inteiros auto incremento (IDF_ID, IDC_ID, IDM_ID etc.)
  - objetivo: identificacao unica, joins rapidos e simplicidade.

- Chaves secundarias/alternativas:
  - email e cpf como chaves candidatas de negocio (UNIQUE);
  - indices em campos de busca/filtro (status, tipo, filial).

- Relacoes:
  - 1:N filial -> fiel
  - 1:N curso -> modulo
  - 1:N curso/modulo -> content
  - N:N fiel <-> curso resolvida por matricula
  - 1:N matricula -> progresso

Essa modelagem reduz redundancia e favorece normalizacao.

## 8) Quais funcoes estudadas poderiam ser usadas no trabalho? (TRIGGERS, JOIN, Procedures..)
Recursos de banco que se aplicam muito bem:

1. JOIN
- para montar telas consolidadas (usuario, curso, status, progresso).

Exemplo:

```sql
SELECT f.IDF_NOME, c.IDC_TITULO, m.IDMATR_STATUS, m.IDMATR_PERCENTUAL
FROM ID_MATRICULA m
JOIN ID_FIEL f ON f.IDF_ID = m.IDF_ID
JOIN ID_CURSO c ON c.IDC_ID = m.IDC_ID;
```

5. TRIGGER
- atualizar automaticamente percentual total da matricula ao inserir/atualizar progresso;
- registrar log de alteracao de status.

3. PROCEDURE
- encapsular regras repetidas:
  - matricular usuario em curso;
  - recalcular progresso completo;
  - listar dashboard personalizado.

4. VIEW
- criar visoes para relatorios sem repetir SQL complexo;
- exemplo: vw_progresso_alunos.

5. EVENT (opcional)
- limpeza periodica de sessoes expiradas.

## 9) O que precisou difundir no back-end em termos gerais?
No backend, foi necessario integrar varios conceitos ao mesmo tempo:

- autenticacao e sessao:
  - controle de acesso por usuario logado;
- validacao de entrada:
  - campos de cadastro e formularios;
- persistencia:
  - consultas SQL para leitura e escrita;
- regra de negocio:
  - organizacao de conteudos, exibicao de videos, busca e perfil;
- navegacao e fluxo:
  - redirecionamentos entre login, dashboard e paginas internas;
- seguranca:
  - necessidade de evolucao para prepared statements, hash de senha e melhor tratamento de erros.

## Fechamento academico
O projeto demonstra aplicacao pratica de estrutura de dados e modelagem relacional em um caso real. A combinacao de entidades (usuarios, cursos, conteudos e progresso), com chaves e relacionamentos, mostra como teoria de banco de dados e estruturas de organizacao da informacao sao fundamentais para construir sistemas funcionais, escalaveis e reutilizaveis.
