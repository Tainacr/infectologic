<?php
session_start();

if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

include_once('config.php');

$email = $_SESSION['email'];
$sql = "SELECT u.id_grupo 
        FROM usuarios u 
        WHERE u.email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($id_grupo);
$stmt->fetch();
$stmt->close();

if ($id_grupo !== 1) {
    header('Location: erro_permissao.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração</title>
    <link rel="stylesheet" href="css/styleadministracao.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="links-container">
        <ul>
            <li><a href="add_usuario.php">Gerenciar usuários</a></li>
            <li><a href="add_conteudo.php">Gerenciar conteúdo</a></li>
            <li><a href="add_curso.php">Gerenciar cursos</a></li>
            <li><a href="add_pergunta.php">Gerenciar perguntas</a></li>
            <li><a href="add_questionario.php">Gerenciar questionários</a></li>
            <li><a href="exibir_comentarios.php">Acessar sugestões</a></li>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dropdown = document.querySelector('.dropdown');
            const dropbtn = document.querySelector('.dropbtn');

            dropbtn.addEventListener('click', () => {
                dropdown.classList.toggle('open');
            });

            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('open');
                }
            });
        });
    </script>
</body>
</html>
