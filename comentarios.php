<?php
session_start();

if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

include_once('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comentario = $_POST['comentario'];
    $emailUsuario = $_SESSION['email'];

    $sql = "INSERT INTO comentarios (email_usuarios, comentario) VALUES (?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ss", $emailUsuario, $comentario);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Comentário registrado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao registrar comentário: " . $stmt->error;
    }
    $stmt->close();
    $conexao->close();

    header('Location: comentarios.php'); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentários</title>
    <link rel="stylesheet" href="css/styleaddcomentario.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="content">
        <h1 class="main-title">Deixe sua sugestão</h1>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
            unset($_SESSION['error_message']);
        }
        ?>

        <form action="comentarios.php" method="POST">
            <label for="comentario">Sugestão:</label>
            <textarea name="comentario" id="comentario" rows="5" required></textarea>
            <button type="submit">Enviar</button>
        </form>

       
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
