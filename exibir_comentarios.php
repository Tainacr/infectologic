<?php
session_start();

if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

include_once('config.php');

$sql_comentarios = "SELECT email_usuarios, comentario FROM comentarios";
$result_comentarios = $conexao->query($sql_comentarios);

$comentarios = [];
if ($result_comentarios->num_rows > 0) {
    $comentarios = $result_comentarios->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugestões</title>
    <link rel="stylesheet" href="css/stylecomentario.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="content">
        <h1 class="main-title">Sugestões</h1>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error_message']) . "</div>";
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="comentarios-list">
            <?php if (count($comentarios) > 0): ?>
                <?php foreach ($comentarios as $comentario): ?>
                    <div class="comentario-item">
                        <p><strong><?php echo htmlspecialchars($comentario['email_usuarios']); ?></strong> sugeriu:</p>
                        <p><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                    </div>
                    <hr>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhuma sugestão encontrada.</p>
            <?php endif; ?>
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
