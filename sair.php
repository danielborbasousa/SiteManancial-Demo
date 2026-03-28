<?php
// Módulo de logout

session_unset(); // Remove todas as variáveis de sessão
session_destroy(); // Destrói a sessão
header("location: index.php"); // Redireciona para a página de login

exit(); // Para a execução