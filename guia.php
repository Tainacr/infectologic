<?php
session_start();
include('navbar.php');

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];

include_once('config.php');

if (isset($_GET['curso_id'])) {
    $curso_id = $_GET['curso_id'];
} else {
    header('Location: homepage.php');
    exit();
}

$sql_curso = "SELECT * FROM cursos WHERE id = ?";
$stmt = $conexao->prepare($sql_curso);
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$result_curso = $stmt->get_result();

if ($result_curso->num_rows > 0) {
    $curso = $result_curso->fetch_assoc();
    $curso_nome = htmlspecialchars($curso['nome']);
    $curso_descricao = htmlspecialchars($curso['descricao']);
} else {
    echo "Curso não encontrado.";
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia - <?php echo $curso_nome; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleguia.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h2 class="card-title"><?php echo $curso_nome; ?></h2>
        <p class="card-description"><?php echo $curso_descricao; ?></p>
        <ul class="course-links">
            <li><a href="exibir_conteudo.php?curso_id=<?php echo $curso_id; ?>">Conteúdo do Curso</a></li>
            <li><a href="questionario.php?curso_id=<?php echo $curso_id; ?>">Questionário</a></li>
        </ul>
    </div>
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
