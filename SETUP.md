Setup rápido — SiteManancial-Demo

Pré-requisitos
- XAMPP (Apache + PHP + MySQL) no Windows (ou ambiente equivalente). PHP 8.x recomendado.
- Acesso ao XAMPP Control Panel para reiniciar o Apache.

Passos para rodar localmente
1. Clone o repositório para `C:\xampp\htdocs\SiteManancial-Demo` (ou coloque a pasta dentro de `htdocs`).
2. Banco de dados:
   - Abra o phpMyAdmin (ou MySQL CLI) e crie um banco.
   - Importe o dump localizado em `database/Banco Igreja Robusto.sql`.
   - Anote nome do banco, usuário e senha.
3. Variáveis de ambiente:
   - Copie `.env.example` para `.env` na raiz do projeto.
   - Preencha `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` e as chaves de e-mail se quiser testar envio.
   - NÃO comite o arquivo `.env`.
4. Permissões de pasta `videos/`:
   - No Windows XAMPP certifique-se de que o usuário que roda o Apache tem permissão de gravação na pasta `videos`.
   - No Linux: `chmod -R 775 videos && chown -R www-data:www-data videos` (ajuste usuário/grupo conforme o seu sistema).
5. Limites de upload (opções):
   - Opção A (rápida): copiar `.user.ini.example` para `.user.ini` e/ou `.htaccess.example` para `.htaccess` na raiz do projeto e reiniciar o Apache (ou aguardar `user_ini.cache_ttl`).
   - Opção B (permanente): editar `C:\xampp\php\php.ini` e ajustar `upload_max_filesize` e `post_max_size` para valores maiores (ex.: 200M). Reinicie o Apache.
6. Reiniciar Apache:
   - Pelo XAMPP Control Panel: Stop → Start no Apache.
   - Ou PowerShell (executar como Administrador):
```powershell
net stop Apache2.4
net start Apache2.4
```
7. Teste rápido:
   - Acesse `http://localhost/SiteManancial-Demo/` e faça login como admin.
   - Vá para `php/admin/admin_conteudos.php` e tente enviar um vídeo.
   - Se falhar, veja a mensagem de erro exibida e os logs do Apache (em `C:\xampp\apache\logs\error.log`).

Notas sobre `.htaccess` e `.user.ini`
- Estes arquivos são per-diretório e servem para sobrescrever diretivas do PHP quando o usuário não tem acesso ao `php.ini` global.
- Nem todos os ambientes processam `.htaccess`/`.user.ini` da mesma forma (depende de como o PHP está integrado ao Apache). Fornecemos os exemplos (`.user.ini.example`, `.htaccess.example`) para você copiar/ajustar localmente.

Segurança
- Não comite `.env` nem chaves sensíveis.
- Depois de testar, se o projeto foi publicado em servidor público, considere usar upload por chunks ou armazenamento externo (S3) e limitar o tamanho permitido.

Problemas comuns
- `UPLOAD_ERR_INI_SIZE` ou erro de formulário: ajustar `upload_max_filesize` / `post_max_size`.
- `Permission denied` ao mover arquivo: ajustar permissões da pasta `videos`.

Se quiser, eu atualizo o `README.md` com um link para este `SETUP.md`.