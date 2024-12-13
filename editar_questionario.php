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

$questionario_atual = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['curso_id']) && !empty($_POST['curso_id'])) {
        $curso_id = $_POST['curso_id'];

        $sql_questionarios = "SELECT id, nome, descricao FROM questionario WHERE curso_id = ?";
        $stmt = $conexao->prepare($sql_questionarios);
        $stmt->bind_param("i", $curso_id);
        $stmt->execute();
        $result_questionarios = $stmt->get_result();

        $questionarios = [];
        if ($result_questionarios->num_rows > 0) {
            $questionarios = $result_questionarios->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
    }

    if (isset($_POST['questionario_id']) && isset($_POST['nome']) && isset($_POST['descricao'])) {
        $questionario_id = $_POST['questionario_id'];
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];

        if (empty($nome) || empty($descricao)) {
            $error_message = "Por favor, preencha todos os campos.";
        } else {
            $sql_update = "UPDATE questionario SET nome = ?, descricao = ? WHERE id = ?";
            $stmt = $conexao->prepare($sql_update);
            $stmt->bind_param("ssi", $nome, $descricao, $questionario_id);

            if ($stmt->execute()) {
                $success_message = "Questionário atualizado com sucesso!";
            } else {
                $error_message = "Erro ao atualizar questionário: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Questionário</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleaddconteudo.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Editar Questionário</h1>

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
                <label for="questionario">Selecione o Questionário</label>
                <select name="questionario_id" id="questionario" required>
                    <option value="">Escolha um questionário</option>
                    <?php foreach ($questionarios as $questionario): ?>
                        <option value="<?php echo $questionario['id']; ?>" <?php echo isset($_POST['questionario_id']) && $_POST['questionario_id'] == $questionario['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($questionario['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <?php if (isset($_POST['questionario_id']) && !empty($_POST['questionario_id'])): ?>
                <?php
                    $questionario_id = $_POST['questionario_id'];
                    $sql_questionario = "SELECT id, nome, descricao FROM questionario WHERE id = ?";
                    $stmt = $conexao->prepare($sql_questionario);
                    $stmt->bind_param("i", $questionario_id);
                    $stmt->execute();
                    $result_questionario = $stmt->get_result();
                    $questionario_atual = $result_questionario->fetch_assoc();
                    $stmt->close();
                ?>
                <?php if ($questionario_atual): ?>
                    <label for="nome">Nome do Questionário:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($questionario_atual['nome']); ?>" required>

                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" rows="5" required><?php echo htmlspecialchars($questionario_atual['descricao']); ?></textarea>

                    <input type="hidden" name="questionario_id" value="<?php echo $questionario_atual['id']; ?>">
                <?php endif; ?>
            <?php endif; ?>

            <button type="submit">Salvar Alterações</button>
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
