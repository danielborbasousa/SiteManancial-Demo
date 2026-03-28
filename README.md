# SiteManancial-Demo

Passo a passo para rodar o projeto localmente com XAMPP.

## 1) Clonar o projeto dentro do htdocs

No Windows PowerShell:

```powershell
cd C:\xampp1\htdocs
git clone <URL_DO_REPOSITORIO> SiteManancial-Demo
```

Se voce ja tem os arquivos, apenas garanta que a pasta final fique assim:

```text
C:\xampp1\htdocs\SiteManancial-Demo
```

## 2) Iniciar o XAMPP

1. Abra o XAMPP Control Panel.
2. Clique em Start no Apache.
3. Clique em Start no MySQL.

## 3) Banco de dados usado no projeto

Estamos rodando o projeto com o banco robusto:

- Banco Igreja Robusto.sql

O arquivo Banco Igreja.txt fica apenas como material didatico para entender como o banco funciona.

## 4) Importar no MySQL Workbench (obrigatorio para este fluxo)

1. Abra o MySQL Workbench.
2. Conecte no seu servidor local (localhost).
3. Va em File > Open SQL Script.
4. Selecione o arquivo Banco Igreja Robusto.sql.
5. Clique no icone de raio (Execute).
6. Confirme se o banco igreja_cursos_v2 foi criado.

## 5) Conferir conexao do projeto

No arquivo `conexao.php`, os dados de conexao devem estar assim (XAMPP padrao):

```php
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "igreja_cursos_v2";
```

Resumo rapido:

- Banco em uso: igreja_cursos_v2 (robusto)
- Banco Igreja.txt: apenas para entendimento da estrutura

## 6) Rodar o projeto

Abra no navegador:

```text
http://localhost/SiteManancial-Demo/index.php
```

## 7) Login de teste

- Email: joao@email.com
- Senha: 123456

ou

- Email: maria@email.com
- Senha: 123456

## 8) Problemas comuns

### Erro de acesso do MySQL (Access denied)

- Verifique se o MySQL esta iniciado no XAMPP.
- Verifique usuario/senha em `conexao.php`.
- No XAMPP padrao, normalmente `root` sem senha.

### Pagina nao abre

- Verifique se o Apache esta iniciado.
- Verifique se a pasta do projeto esta em `C:\xampp1\htdocs\SiteManancial-Demo`.
- Abra exatamente a URL: `http://localhost/SiteManancial-Demo/index.php`.
