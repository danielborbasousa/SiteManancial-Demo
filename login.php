<?php
session_start();
include("php/conexao.php");

$erro = "";
$base_url = "http://localhost/SiteManancial-Demo/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["IDF_EMAIL"] ?? "");
    $email_limpo = mysqli_real_escape_string($conn, $email);

    // Procurar usuário no banco para resolver nome e flag de admin (se existir)
    $sql_lookup = "SELECT f.IDF_ID, f.IDF_NOME, CASE WHEN a.IDA_ID IS NULL THEN 0 ELSE 1 END AS EH_ADMIN FROM ID_FIEL f LEFT JOIN ID_ADMIN a ON a.IDA_FIEL_ID = f.IDF_ID AND a.IDA_ATIVO = 1 WHERE LOWER(f.IDF_EMAIL) = LOWER('$email_limpo') LIMIT 1";
    $res_lookup = @mysqli_query($conn, $sql_lookup);
    $usuario_db = null;
    $eh_admin = false;
    $id_fiel = 1;

    if ($res_lookup && mysqli_num_rows($res_lookup) > 0) {
        $usuario_db = mysqli_fetch_assoc($res_lookup);
        $id_fiel = (int) ($usuario_db['IDF_ID'] ?? 1);
        $nome_usuario = $usuario_db['IDF_NOME'] ?? $email_limpo;
        $eh_admin = ((int) ($usuario_db['EH_ADMIN'] ?? 0) === 1);
    } else {
        $nome_usuario = $email_limpo !== '' ? $email_limpo : 'Usuário';
    }

    session_regenerate_id(true);
    $_SESSION["Usuario_id"] = $id_fiel;
    $_SESSION["Usuario_logado"] = $email_limpo !== "" ? $email_limpo : "usuario@local";
    $_SESSION["Usuario_nome"] = $nome_usuario;
    $_SESSION["Usuario_tipo"] = $eh_admin ? 'admin' : 'fiel';
    $_SESSION["Usuario_token"] = bin2hex(random_bytes(32));
    $_SESSION["Usuario_login_at"] = time();
    $_SESSION["Usuario_last_activity"] = time();

    auth_store_session($conn);

    if ($eh_admin) {
        header("Location: php/admin/admin_conteudos.php");
    } else {
        header("Location: php/fiel/dashboard.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Missão Evangélica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="js/theme.js"></script>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="position-absolute top-0 end-0 p-3" style="z-index: 50;">
        <div class="theme-toggle-container">
            <i class="fas fa-moon theme-icon"></i>
            <input type="checkbox" id="theme-toggle" class="theme-toggle" aria-label="Alternar tema">
            <i class="fas fa-sun theme-icon"></i>
        </div>
    </div>

    <div class="auth-container text-center">
        <a href="<?php echo $base_url; ?>index.html" class="d-inline-block text-decoration-none mb-4">
            <img src="assets/logo.png" alt="Logotipo" class="logo" style="height: 80px; width: auto;" />
        </a>
        <h2>Bem-vindo</h2>
        <p>Acesse sua conta para continuar</p>
        
        <?php if ($erro != "") { 
            echo "<div class='error-message'><i class='fas fa-exclamation-circle'></i> " . htmlspecialchars($erro) . "</div>"; 
        } ?>
        
        <form action="" method="POST">
            <div class="mb-3">
                <input type="email" name="IDF_EMAIL" class="form-control custom-input" placeholder="Seu e-mail" autocomplete="email" maxlength="100" required>
            </div>

            <div class="mb-2">
                <div class="input-group">
                    <input type="password" name="IDF_SENHA" id="loginPasswordInput" class="form-control custom-input" placeholder="Sua senha" autocomplete="current-password" maxlength="100" required>
                    <button class="btn btn-outline-light" type="button" id="toggleLoginPassword" style="border: 2px solid var(--border-color); color: var(--text-main);">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="password-help mb-4">
                <i class="fas fa-info-circle"></i> 
                Use suas credenciais do <strong>banco de dados da Missão Manancial da Esperança</strong>. 
                Se não tem acesso, converse com um administrador.
            </div>

            <button type="submit" class="btn btn-light w-100 py-2 fw-bold mb-4">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>

            <hr class="my-3" style="border-color: var(--border-color);">

            <p class="text-center mb-2">
                Novo aqui? <a href="<?php echo $base_url; ?>php/fiel/register.php" class="auth-link">Criar uma conta</a>
            </p>
            <p class="text-center mb-0">
                <a href="<?php echo $base_url; ?>index.html" class="auth-link"><i class="fas fa-arrow-left"></i> Voltar para a página inicial</a>
            </p>
        </form>
    </div>

</body>
<script>
    (function () {
        const passwordInput = document.getElementById('loginPasswordInput');
        const toggleButton = document.getElementById('toggleLoginPassword');
        if (!passwordInput || !toggleButton) return;

        toggleButton.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            toggleButton.querySelector('i').className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
        });
    })();
</script>
</html>