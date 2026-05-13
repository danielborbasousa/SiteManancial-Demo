<?php
// Legacy route: always redirect to the canonical dashboard
session_start();
include('../conexao.php');
auth_require();
header('Location: dashboard.php');
exit;
?>