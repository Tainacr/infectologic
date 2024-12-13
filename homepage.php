<?php
session_start();
include('navbar.php');
if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];

include_once('config.php');

$sql = "SELECT primeironome, foto_perfil FROM usuarios WHERE email = ?";
$stmt = $conexao->prepare($sql);
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conexao->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $primeironome = htmlspecialchars($usuario['primeironome'] ?? 'Usuário');
    $foto_perfil = !empty($usuario['foto_perfil']) 
        ? htmlspecialchars($usuario['foto_perfil']) 
        : 'img/default_profile.png';
} else {
    header('Location: login.php');
    exit();
}

$stmt->close();

$sql_cursos = "SELECT * FROM cursos";
$result_cursos = $conexao->query($sql_cursos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Acadêmica</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/stylehomepage.css">
</head>
<body>

    <div class="content">
        <h1 class="main-title">CONTEÚDO</h1>
        <hr class="title-underline">
        <div class="card-container">
            <?php if ($result_cursos->num_rows > 0): ?>
                <?php while ($curso = $result_cursos->fetch_assoc()): ?>
                    <div class="card">
                        <div class="card-header">
                            <img src="img/livro.png" alt="Livro" class="card-icon">
                            <span class="card-title"><?php echo htmlspecialchars($curso['nome']); ?></span>
                        </div>
                        <hr class="card-line">
                        <ul class="card-content">
                            <li><?php echo htmlspecialchars($curso['descricao']); ?></li>
                        </ul>
                        <button class="start-button" onclick="window.location.href='guia.php?curso_id=<?php echo $curso['id']; ?>'">INICIAR</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhum curso disponível no momento.</p>
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

<?php
$conexao->close();
?>
