<?php
// Script de setup - adiciona vídeos ao banco de dados
include("conexao.php");

// Limpar vídeos antigos
$limpar = "DELETE FROM ID_CONTENT WHERE IDCT_TIPO = 'video' OR IDCT_TIPO = 'VIDEO'";
mysqli_query($conn, $limpar);

// Adicionar vídeo local de teste
$sql = "INSERT INTO ID_CONTENT (IDC_ID, IDM_ID, IDCT_TIPO, IDCT_TITULO, IDCT_DESCRICAO, IDCT_URL, IDCT_ORDEM) VALUES
(1, 1, 'VIDEO', 'Video de teste', 'Arquivo local para demonstracao', 'videos/Neymar.MP4', 1)";

if(mysqli_query($conn, $sql)) {
    echo "✓ Vídeos adicionados com sucesso!";
} else {
    echo "✗ Erro: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
