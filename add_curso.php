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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];

    $sql = "INSERT INTO cursos (nome, descricao) VALUES (?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ss", $nome, $descricao);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Curso adicionado com sucesso!";  
    } else {
        $_SESSION['error_message'] = "Erro ao adicionar curso.";  
    }
    $stmt->close();
    $conexao->close();

    header('Location: add_curso.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cursos</title>
    <link rel="stylesheet" href="css/styleaddcurso.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="content">
        <h1 class="main-title">Gerenciar Cursos</h1>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo "<div class='alert success'>" . $_SESSION['success_message'] . "</div>";
            unset($_SESSION['success_message']);  
        }

        if (isset($_SESSION['error_message'])) {
            echo "<div class='alert error'>" . $_SESSION['error_message'] . "</div>";
            unset($_SESSION['error_message']);
        }
        ?>

        <form action="add_curso.php" method="POST">
            <label for="nome">Nome do Curso</label>
            <input type="text" name="nome" id="nome" required>

            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" required></textarea>

            <button type="submit">Adicionar Curso</button>
        </form>
    </div>

    <div class="btn">
        <a href="editar_curso.php" class="btn-editar">Editar</a>
        <a href="excluir_curso.php" class="btn-excluir">Excluir</a>
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
