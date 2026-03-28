# SiteManancial-Demo

Guia rapido para baixar e rodar localmente.

## Requisitos

- XAMPP (Apache + MySQL)
- Git

## 1) Baixar o projeto

No PowerShell:

```powershell
cd C:\xampp1\htdocs
git clone <URL_DO_REPOSITORIO> SiteManancial-Demo
cd SiteManancial-Demo
```

## 2) Iniciar servicos

No XAMPP Control Panel:

1. Start em Apache
2. Start em MySQL

## 3) Criar banco de dados

Arquivo recomendado para este projeto: Banco Igreja.txt.

No MySQL Workbench (ou phpMyAdmin), execute o SQL do arquivo Banco Igreja.txt.
Esse script cria o banco igreja_cursos e popula dados de teste.

## 4) Ajustar conexao

No arquivo conexao.php, use:

```php
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "igreja_cursos";
```

Se sua maquina ja estiver usando outro nome de banco, ajuste apenas o valor de $banco.

## 5) Rodar o sistema

Abra no navegador:

```text
http://localhost/SiteManancial-Demo/index.php
```

## 6) Login de teste

- joao@email.com / 123456
- maria@email.com / 123456

## 7) Videos internos (sem YouTube)

1. Coloque o arquivo de video em videos/
2. Cadastre no banco em ID_CONTENT com IDCT_TIPO = 'video' e IDCT_URL = caminho do arquivo

Exemplo:

```sql
INSERT INTO ID_CONTENT (IDC_ID, IDM_ID, IDCT_TIPO, IDCT_TITULO, IDCT_DESCRICAO, IDCT_URL, IDCT_ORDEM)
VALUES
(1, 1, 'video', 'Video de teste', 'Arquivo local para demonstracao', 'videos/Neymar.MP4', 1);
```

## 8) Problemas comuns

- Access denied MySQL: confira usuario/senha e se o MySQL iniciou no XAMPP
- Pagina nao abre: confira Apache iniciado e caminho em C:\xampp1\htdocs\SiteManancial-Demo
