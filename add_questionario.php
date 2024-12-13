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
    $curso_id = $_POST['curso_id'];

    $sql = "INSERT INTO questionario (nome, descricao, curso_id) VALUES (?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssi", $nome, $descricao, $curso_id);

    if ($stmt->execute()) {
        echo "Questionário adicionado com sucesso!";
    } else {
        echo "Erro ao adicionar questionário.";
    }

    $stmt->close();
}

$sql = "SELECT * FROM cursos";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$cursos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Questionários</title>
    <link rel="stylesheet" href="css/styleaddquest.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="content">
        <h1 class="main-title">Gerenciar Questionários</h1>
        <form action="add_questionario.php" method="POST">
            <label for="nome">Nome do Questionário</label>
            <input type="text" name="nome" id="nome" required>

            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" required></textarea>

            <label for="curso_id">Curso</label>
            <select name="curso_id" id="curso_id" required>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?php echo $curso['id']; ?>"><?php echo $curso['nome']; ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Adicionar Questionário</button>
        </form>
    </div>

    <div class="btn">
        <a href="editar_questionario.php" class="btn-editar">Editar</a>
        <a href="excluir_questionario.php" class="btn-excluir">Excluir</a>
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
