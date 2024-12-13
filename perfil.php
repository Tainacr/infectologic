<?php
session_start();
include('navbar.php');

if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];

include_once('config.php');

$sql = "SELECT primeironome, sobrenome, foto_perfil FROM usuarios WHERE email = ?";
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
    $sobrenome = htmlspecialchars($usuario['sobrenome'] ?? '');
    $foto_perfil = !empty($usuario['foto_perfil']) 
        ? htmlspecialchars($usuario['foto_perfil']) 
        : 'img/default_profile.png';
} else {
    header('Location: login.php');
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - InfectoLogic</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleperfil.css">
</head>
<body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropbtn = document.querySelector('.dropbtn');
        const dropdown = document.querySelector('.dropdown');

        dropbtn.addEventListener('click', function() {
            dropdown.classList.toggle('open');
        });

        window.addEventListener('click', function(event) {
            if (!dropdown.contains(event.target) && !dropbtn.contains(event.target)) {
                dropdown.classList.remove('open');
            }
        });
    });
</script>

    <div class="content">
        <h1 class="main-title">PERFIL</h1>

        <div class="perfil-container">
            <div class="foto-perfil">
                <form action="upload_foto.php" method="post" enctype="multipart/form-data" style="display: inline;">
                    <label for="foto">
                        <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de Perfil" style="cursor: pointer; border-radius: 50%; width: 100px; height: 100px; object-fit: cover;">
                    </label>
                    <input type="file" name="foto" id="foto" accept="image/*" style="display: none;" onchange="this.form.submit();" required>
                </form>
            </div>
            <h1><?php echo htmlspecialchars($primeironome) . ' ' . htmlspecialchars($sobrenome); ?></h1>

            <div class="curso-container">
                <h2>Cursos em andamento:</h2>
                <ul>
                    <?php
                    $cursos_sql = "SELECT * FROM cursos WHERE id = (SELECT id FROM usuarios WHERE email = ?)";
                    $cursos_stmt = $conexao->prepare($cursos_sql);

                    if (!$cursos_stmt) {
                        die("Erro na preparação da consulta de cursos: " . $conexao->error);
                    }

                    $cursos_stmt->bind_param("s", $email);
                    $cursos_stmt->execute();
                    $cursos_result = $cursos_stmt->get_result();

                    if ($cursos_result->num_rows > 0) {
                        while ($cursos = $cursos_result->fetch_assoc()) {
                            echo "<li><a href='curso.php?id=" . htmlspecialchars($cursos['id']) . "'>" . htmlspecialchars($cursos['nome']) . "</a></li>";
                        }
                    } else {
                        echo "<li>Você ainda não tem cursos cadastrados.</li>";
                    }

                    $cursos_stmt->close();
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <?php
    $conexao->close();
    ?>

</body>
</html>
