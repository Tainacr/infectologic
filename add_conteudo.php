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

$sql_cursos = "SELECT id, nome FROM cursos"; 
$result_cursos = $conexao->query($sql_cursos);

if ($result_cursos->num_rows > 0) {
    $cursos = $result_cursos->fetch_all(MYSQLI_ASSOC);
} else {
    $cursos = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $curso_id = $_POST['curso_id'];
    $titulo = $_POST['titulo'];
    $material = $_POST['material'];

    if (empty($curso_id) || empty($titulo) || empty($material)) {
        $error_message = "Por favor, preencha todos os campos.";
    } else {
        $sql = "INSERT INTO conteudo (curso_id, titulo, material) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iss", $curso_id, $titulo, $material);

        if ($stmt->execute()) {
            $success_message = "Conteúdo adicionado com sucesso!";
        } else {
            $error_message = "Erro ao adicionar conteúdo: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Conteúdo</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleaddconteudo.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Gerenciar Conteúdo</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="curso">Selecione o Curso</label>
            <select name="curso_id" id="curso" required>
                <option value="">Escolha um curso</option> 
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?php echo $curso['id']; ?>"><?php echo htmlspecialchars($curso['nome']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="titulo">Título do Conteúdo:</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="material">Material:</label>
            <textarea id="material" name="material" rows="5" required></textarea>

            <button type="submit">Adicionar Conteúdo</button>

            <div class= "btn">
            <a href="editar_conteudo.php" class="btn-editar">Editar</a>
            <a href="excluir_conteudo.php" class="btn-excluir">Excluir</a>
            </div>

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
