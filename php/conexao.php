<?php
// Módulo de conexão com o banco de dados

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!defined('SITE_BASE_URL')) {
    define('SITE_BASE_URL', '/SiteManancial-Demo/');
}

if (!defined('SESSION_TIMEOUT_SECONDS')) {
    define('SESSION_TIMEOUT_SECONDS', 3600);
}

function site_load_env($path = null) {
    $env = array();
    $path = $path ?? dirname(__DIR__) . '/.env';

    if (!is_file($path) || !is_readable($path)) {
        return $env;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $env;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $key = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        if ($key === '') {
            continue;
        }

        if ((strlen($value) >= 2) && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
            $value = substr($value, 1, -1);
        }

        $env[$key] = $value;
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv($key . '=' . $value);
    }

    return $env;
}

function site_env($key, $default = '') {
    if (array_key_exists($key, $_ENV) && $_ENV[$key] !== '') {
        return $_ENV[$key];
    }

    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }

    if (array_key_exists($key, $_SERVER) && $_SERVER[$key] !== '') {
        return $_SERVER[$key];
    }

    return $default;
}

site_load_env();

// Email provider configuration - prefer generic names, but keep backward compatibility
if (!defined('EMAIL_API_KEY')) {
    $email_api = site_env('EMAIL_API_KEY');
    if (empty($email_api)) {
        $email_api = site_env('BREVO_API_KEY');
    }
    define('EMAIL_API_KEY', $email_api);
}

if (!defined('EMAIL_SENDER_EMAIL')) {
    $sender = site_env('EMAIL_SENDER_EMAIL');
    if (empty($sender)) {
        $sender = site_env('BREVO_SENDER_EMAIL');
    }
    define('EMAIL_SENDER_EMAIL', $sender);
}

if (!defined('EMAIL_SENDER_NAME')) {
    $sender_name = site_env('EMAIL_SENDER_NAME', site_env('BREVO_SENDER_NAME', 'SiteManancial'));
    define('EMAIL_SENDER_NAME', $sender_name);
}

function site_url($path = '') {
    return rtrim(SITE_BASE_URL, '/') . '/' . ltrim($path, '/');
}

function enviar_email_sistema($destinatario, $assunto, $mensagem_html, $mensagem_texto = '') {
    $destinatario = trim((string) $destinatario);
    if ($destinatario === '') {
        return false;
    }

    $api_key = trim((string) EMAIL_API_KEY);
    $sender_email = trim((string) EMAIL_SENDER_EMAIL);

    if ($api_key !== '' && $sender_email !== '' && function_exists('curl_init')) {
        $payload = array(
            'sender' => array(
                'name' => trim((string) EMAIL_SENDER_NAME) !== '' ? EMAIL_SENDER_NAME : 'SiteManancial',
                'email' => $sender_email,
            ),
            'to' => array(
                array(
                    'email' => $destinatario,
                ),
            ),
            'subject' => $assunto,
            'htmlContent' => $mensagem_html,
        );

        if ($mensagem_texto !== '') {
            $payload['textContent'] = $mensagem_texto;
        }

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'api-key: ' . $api_key,
                'Content-Type: application/json',
                'Accept: application/json',
            ),
            CURLOPT_TIMEOUT => 20,
        ));

        $response = curl_exec($ch);
        $curl_error = curl_errno($ch);
        $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$curl_error && $http_code >= 200 && $http_code < 300) {
            return true;
        }
    }

    if (!function_exists('mail')) {
        return false;
    }

    $assunto_formatado = '=?UTF-8?B?' . base64_encode($assunto) . '?=';
    $headers = array();
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . (trim((string) BREVO_SENDER_NAME) !== '' ? BREVO_SENDER_NAME : 'SiteManancial') . ' <' . (trim((string) BREVO_SENDER_EMAIL) !== '' ? BREVO_SENDER_EMAIL : 'no-reply@localhost') . '>';
    $headers[] = 'Reply-To: ' . (trim((string) BREVO_SENDER_EMAIL) !== '' ? BREVO_SENDER_EMAIL : 'no-reply@localhost');
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    $html = $mensagem_html;
    if ($mensagem_texto !== '') {
        $html .= '<hr><pre style="font-family: Arial, sans-serif; white-space: pre-wrap; color: #334155;">' . htmlspecialchars($mensagem_texto) . '</pre>';
    }

    return @mail($destinatario, $assunto_formatado, $html, implode("\r\n", $headers));
}

function auth_logout($reason = '') {
    global $conn;

    if (isset($_SESSION['Usuario_token']) && isset($_SESSION['Usuario_id'])) {
        $token = mysqli_real_escape_string($conn, (string) $_SESSION['Usuario_token']);
        $usuario_id = (int) $_SESSION['Usuario_id'];
        @mysqli_query($conn, "UPDATE ID_SESSAO SET IDS_REVOGADA = 1 WHERE IDS_TOKEN = '$token' AND IDF_ID = $usuario_id LIMIT 1");
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    session_unset();
    session_destroy();

    $query = $reason !== '' ? '?reason=' . urlencode($reason) : '';
    header('Location: ' . site_url('login.php') . $query);
    exit;
}

function auth_store_session($database = null) {
    global $conn;
    $database = $database ?? $conn;

    $usuario_id = auth_resolve_fiel_id();
    if (!$database || $usuario_id <= 0 || !isset($_SESSION['Usuario_token'])) {
        return;
    }

    $token = mysqli_real_escape_string($database, (string) $_SESSION['Usuario_token']);
    $ip = mysqli_real_escape_string($database, $_SERVER['REMOTE_ADDR'] ?? '');
    $agent = mysqli_real_escape_string($database, $_SERVER['HTTP_USER_AGENT'] ?? '');

    $sql_upsert = "
        INSERT INTO ID_SESSAO (IDF_ID, IDS_TOKEN, IDS_IP, IDS_USER_AGENT, IDS_EXPIRA_EM, IDS_REVOGADA)
        VALUES ($usuario_id, '$token', '$ip', '$agent', DATE_ADD(NOW(), INTERVAL 1 HOUR), 0)
        ON DUPLICATE KEY UPDATE
            IDF_ID = VALUES(IDF_ID),
            IDS_IP = VALUES(IDS_IP),
            IDS_USER_AGENT = VALUES(IDS_USER_AGENT),
            IDS_EXPIRA_EM = VALUES(IDS_EXPIRA_EM),
            IDS_REVOGADA = 0
    ";
    @mysqli_query($database, $sql_upsert);
}

function auth_resolve_fiel_id() {
    global $conn;

    if (!isset($_SESSION['Usuario_id'])) {
        return 0;
    }

    $usuario_id = (int) $_SESSION['Usuario_id'];

    $sql_fiel = "SELECT IDF_ID FROM ID_FIEL WHERE IDF_ID = $usuario_id LIMIT 1";
    $res_fiel = mysqli_query($conn, $sql_fiel);
    if ($res_fiel && mysqli_num_rows($res_fiel) > 0) {
        return $usuario_id;
    }

    $sql_admin = "SELECT IDA_FIEL_ID FROM ID_ADMIN WHERE IDA_ID = $usuario_id LIMIT 1";
    $res_admin = mysqli_query($conn, $sql_admin);
    if ($res_admin && mysqli_num_rows($res_admin) > 0) {
        $row_admin = mysqli_fetch_assoc($res_admin);
        $resolved_id = (int) ($row_admin['IDA_FIEL_ID'] ?? 0);
        if ($resolved_id > 0) {
            $_SESSION['Usuario_id'] = $resolved_id;
            return $resolved_id;
        }
    }

    return 0;
}

function auth_touch($database = null) {
    global $conn;
    $database = $database ?? $conn;
    
    if (!isset($_SESSION['Usuario_logado'])) {
        return;
    }

    if (!isset($_SESSION['Usuario_login_at'])) {
        $_SESSION['Usuario_login_at'] = time();
    }

    $_SESSION['Usuario_last_activity'] = time();
    
    // Atualizar last_activity no banco de dados
    $usuario_id = auth_resolve_fiel_id();

    if ($database && $usuario_id > 0) {
        $sql_touch = "UPDATE ID_FIEL SET IDF_LAST_ACTIVITY = NOW() WHERE IDF_ID = $usuario_id LIMIT 1";
        @mysqli_query($database, $sql_touch);
        auth_store_session($database);
    }
}

function auth_require(array $allowedRoles = array()) {
    global $conn;
    
    if (!isset($_SESSION['Usuario_logado'])) {
        auth_logout();
    }

    if (!empty($allowedRoles)) {
        $role = $_SESSION['Usuario_tipo'] ?? '';
        if (!in_array($role, $allowedRoles, true)) {
            if ($role === 'admin') {
                header('Location: ' . site_url('php/admin/admin_conteudos.php'));
            } else {
                header('Location: ' . site_url('php/fiel/dashboard.php'));
            }
            exit;
        }
    }
}

if (!defined('MODO_BANCO')) {
    define('MODO_BANCO', 'simples'); // troque para 'robusto' para usar o SQL completo
}

function banco_eh_robusto() {
    return MODO_BANCO === 'robusto';
}

function tabela_tem_coluna($conn, $tabela, $coluna) {
    static $cache = array();
    $chave = $tabela . '.' . $coluna;

    if (isset($cache[$chave])) {
        return $cache[$chave];
    }

    $tabela_sql = mysqli_real_escape_string($conn, $tabela);
    $coluna_sql = mysqli_real_escape_string($conn, $coluna);
    $sql = "SHOW COLUMNS FROM $tabela_sql LIKE '$coluna_sql'";
    $resultado = mysqli_query($conn, $sql);
    $cache[$chave] = ($resultado && mysqli_num_rows($resultado) > 0);

    return $cache[$chave];
}

$host = "localhost"; // Define o host do banco de dados
$usuario = "root"; // Define o usuário do banco de dados
$senha = ""; // Define a senha do banco de dados
$banco = banco_eh_robusto() ? "igreja_cursos_v2" : "igreja_cursos"; // Define o nome do banco de dados

$conn = mysqli_connect($host, $usuario, $senha, $banco); // Estabelece a conexão com o banco de dados
if(!$conn) {
     die("Erro ao conectar: " . mysqli_connect_error()); // Exibe erro e para a execução
}

mysqli_set_charset($conn, "utf8mb4");
?>