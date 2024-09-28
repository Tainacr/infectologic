<?php
    session_start();
    if  ((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true)){
        header('Location: login.php');

    unset($_SESSION['email']);
    unset($_SESSION['senha']);
}
    $logado = $_SESSION['email'];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfectoLogic</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/stylehomepage.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <div class="dropdown">
                <button class="dropbtn">CONTEÚDO ▼</button>
                <div class="dropdown-content">
                    <a href="#">Login</a>
                    <a href="#">Cadastro</a>
                </div>
            </div>
            <div class="search-bar">
                <input type="text" placeholder="Buscar conteúdo">
                <img src="img/lupa.png" alt="lupa" class="search-icon">
            </div>
        </div>
        <div class="navbar-center">
            <img src="img/bacteriofago.png" alt="Logo" class="site-logo">
            <span class="site-title">INFECTOLOGIC</span>
        </div>
        <div class="navbar-right">
            <img src="img/perfil.png" alt="Perfil" class="profile-icon">
            <a href="#" class="profile-link">NOME</a>
        </div>
    </nav>

    <div class="content">
        <h1 class="main-title">CONTEÚDO</h1>
        <hr class="title-underline">
        <div class="card-container">
            <div class="card">
                <div class="card-header">
                    <img src="img/livro.png" alt="Livro" class="card-icon">
                    <span class="card-title">UNIDADE 1: INTRODUÇÃO À TEORIA MICROBIANA DA DOENÇA</span>
                    <span class="progress">PROGRESSO: 0%</span>
                </div>
                <hr class="card-line">
                <ul class="card-content">
                    <li>HISTÓRIA DA MICROBIOLOGIA</li>
                    <li>FUNDAMENTOS DA MICROBIOLOGIA</li>
                    <li>LOUIS PASTEUR, ROBERT KOCH E ANTONIE VAN LEEUWENHOEK</li>
                </ul>
                <button class="start-button">INICIAR</button>
            </div>
            <div class="card">
                <div class="card-header">
                    <img src="img/livro.png" alt="Livro" class="card-icon">
                    <span class="card-title">UNIDADE 2: ESTUDOS DE CASO</span>
                    <span class="progress">PROGRESSO: 0%</span>
                </div>
                <hr class="card-line">
                <ul class="card-content">
                    <li>SURTOS HISTÓRICOS</li>
                    <li>DOENÇAS INFECCIOSAS NOTÁVEIS</li>
                </ul>
                <button class="start-button">INICIAR</button>
            </div>
        </div>
    </div>
</body>
</html>
