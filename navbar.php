<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylenavbar.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <div class="dropdown">
                <button class="dropbtn">CONTEÚDO ▼</button>
                <div class="dropdown-content">
                    <a href="homepage.php">Cursos</a>  
                    <a href="administracao.php">Administrar</a>
                    <a href="comentarios.php">Sugestões</a>
                    <a href="login.php">Logout</a>
                  

                </div>
            </div>
        </div>
        <div class="navbar-center">
            <img src="img/bacteriofago.png" alt="Logo" class="site-logo">
            <span class="site-title">INFECTOLOGIC</span>
        </div>
        <div class="navbar-right">
            <img src="<?php echo $foto_perfil; ?>" alt="Perfil" class="profile-icon">
            <a href="perfil.php" class="profile-link"><?php echo $primeironome; ?></a>
        </div>
    </nav>
</body>
</html>
