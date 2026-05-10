# SiteManancial-Demo

Plataforma simples em PHP para exibir conteudos da igreja, com area de login, painel de usuario, area administrativa para videos e banco de dados em duas versoes: uma simples para demonstracao e outra robusta para apresentar a evolucao do projeto.

## Estrutura principal
- `login.php`: entrada da aplicacao.
- `php/fiel/`: area do usuario comum.
- `php/admin/`: area administrativa de conteudos.
- `php/conexao.php`: configuracao do banco e chave `MODO_BANCO`.
- `assets/`: imagens e recursos visuais.
- `database/`: scripts SQL do banco simples e do banco robusto.

## Como rodar
1. Inicie o Apache e o MySQL no XAMPP.
2. Importe o banco desejado:
   - `database/Banco Igreja.txt` para a versao basica.
   - `database/Banco Igreja Robusto.sql` para a versao robusta.
3. Ajuste `php/conexao.php` se precisar trocar usuario, senha ou o modo do banco.
4. Abra no navegador:

```text
http://localhost/SiteManancial-Demo/login.php
```

## Modo simples e modo robusto
No arquivo `php/conexao.php`, altere:

```php
define('MODO_BANCO', 'simples');
```

- `simples`: usa a estrutura mais basica, indicada para a entrega e explicacao em sala.
- `robusto`: usa o modelo com melhor organizacao, hash de senha e relacoes mais completas.

## Login de teste
- `joao@email.com` / `123456`
- `maria@email.com` / `123456`

## Observacoes
- Os videos devem ficar em `videos/`.
- O admin consegue enviar, listar, editar e excluir conteudos na area administrativa.
- O projeto foi mantido com abordagem simples para facilitar a apresentacao da disciplina.