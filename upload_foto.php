<?php
session_start();
include_once('config.php'); 

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $arquivo = $_FILES['foto'];
    
    $diretorio = 'uploads/';
    $nomeArquivo = basename($arquivo['name']);
    $caminhoArquivo = $diretorio . uniqid() . '_' . $nomeArquivo;

    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0755, true); 
    }

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoArquivo)) {
        $sql = "UPDATE usuarios SET foto_perfil = '$caminhoArquivo' WHERE email = '$email'";
        if ($conexao->query($sql) === TRUE) {
            header('Location: perfil.php');
            exit();
        } else {
            echo "Erro ao atualizar a foto: " . $conexao->error;
        }
    } else {
        echo "Erro ao fazer upload do arquivo.";
    }
} else {
    echo "Nenhum arquivo enviado ou ocorreu um erro.";
}
?>
