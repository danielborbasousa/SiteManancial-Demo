<?php
// Módulo de conexão com o banco de dados

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