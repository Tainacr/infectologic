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

$conteudos = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['conteudo_id']) && !empty($_POST['conteudo_id'])) {
        $conteudo_id = $_POST['conteudo_id'];

        $sql_delete = "DELETE FROM conteudo WHERE id = ?";
        $stmt = $conexao->prepare($sql_delete);
        $stmt->bind_param("i", $conteudo_id);

        if ($stmt->execute()) {
            $success_message = "Conteúdo excluído com sucesso!";
        } else {
            $error_message = "Erro ao excluir conteúdo: " . $stmt->error;
        }

        $stmt->close();
    }

    // Carregar os conteúdos apenas após o curso ser selecionado
    if (isset($_POST['curso_id']) && !empty($_POST['curso_id'])) {
        $sql_conteudos = "SELECT id, titulo FROM conteudo WHERE curso_id = ?";
        $stmt = $conexao->prepare($sql_conteudos);
        $stmt->bind_param("i", $_POST['curso_id']);
        $stmt->execute();
        $result_conteudos = $stmt->get_result();
        if ($result_conteudos->num_rows > 0) {
            $conteudos = $result_conteudos->fetch_all(MYSQLI_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Conteúdo</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleaddconteudo.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Excluir Conteúdo</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="curso">Selecione o Curso</label>
            <select name="curso_id" id="curso" required onchange="this.form.submit()">
                <option value="">Escolha um curso</option> 
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?php echo $curso['id']; ?>" <?php echo isset($_POST['curso_id']) && $_POST['curso_id'] == $curso['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($curso['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (isset($_POST['curso_id']) && !empty($_POST['curso_id'])): ?>
                <label for="conteudo">Selecione o Conteúdo para Excluir:</label>
                <select name="conteudo_id" id="conteudo" required>
                    <option value="">Escolha um conteúdo</option>
                    <?php foreach ($conteudos as $conteudo): ?>
                        <option value="<?php echo $conteudo['id']; ?>"><?php echo htmlspecialchars($conteudo['titulo']); ?></option>
                    <?php endforeach; ?>
                </select>
                
            <?php endif; ?>
            <button type="submit">Excluir Conteúdo</button>
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
