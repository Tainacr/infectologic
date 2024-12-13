<?php
session_start();

if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

include_once('config.php');

if (isset($_GET['curso_id']) && !empty($_GET['curso_id'])) {
    $curso_id = $_GET['curso_id'];

    $sql = "SELECT c.titulo, c.material, cu.nome AS curso_nome 
            FROM conteudo c
            INNER JOIN cursos cu ON c.curso_id = cu.id
            WHERE c.curso_id = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $conteudos = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $error_message = "Nenhum conteúdo encontrado para este curso.";
    }

    $stmt->close();
} else {
    $error_message = "Curso não especificado.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conteúdos do Curso</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleconteudo.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Conteúdos do Curso</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($conteudos)): ?>
            <div class="conteudos-list">
                <?php foreach ($conteudos as $conteudo): ?>
                    <div class="conteudo-item">
                        <h2><?php echo htmlspecialchars($conteudo['titulo']); ?></h2>
                        <div class="material">
                            <p><?php echo nl2br(htmlspecialchars($conteudo['material'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
