<?php
// Módulo de conexão com o banco de dados


$host = "localhost"; // Define o host do banco de dados
$usuario = "root"; // Define o usuário do banco de dados
$senha = ""; // Define a senha do banco de dados
$banco = "igreja_cursos_v2"; // Define o nome do banco de dados

$conn = mysqli_connect($host,$usuario,$senha,$banco); // Estabelece a conexão com o banco de dados
if(!$conn) // Verifica se a conexão falhou
    {
     die("Erro ao conectar: " . mysqli_connect_error()); // Exibe erro e para a execução
    }






?>