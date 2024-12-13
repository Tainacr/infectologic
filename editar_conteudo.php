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
$conteudo_atual = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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

    if (isset($_POST['conteudo_id']) && !empty($_POST['conteudo_id'])) {
        $conteudo_id = $_POST['conteudo_id'];

        $sql_conteudo = "SELECT id, titulo, material FROM conteudo WHERE id = ?";
        $stmt = $conexao->prepare($sql_conteudo);
        $stmt->bind_param("i", $conteudo_id);
        $stmt->execute();
        $result_conteudo = $stmt->get_result();
        if ($result_conteudo->num_rows > 0) {
            $conteudo_atual = $result_conteudo->fetch_assoc();
        }
        $stmt->close();
    }

    if (isset($_POST['titulo']) && isset($_POST['material']) && isset($_POST['conteudo_id'])) {
        $titulo = $_POST['titulo'];
        $material = $_POST['material'];
        $conteudo_id = $_POST['conteudo_id'];

        if (empty($titulo) || empty($material)) {
            $error_message = "Por favor, preencha todos os campos.";
        } else {
            $sql_update = "UPDATE conteudo SET titulo = ?, material = ? WHERE id = ?";
            $stmt = $conexao->prepare($sql_update);
            $stmt->bind_param("ssi", $titulo, $material, $conteudo_id);

            if ($stmt->execute()) {
                $success_message = "Conteúdo atualizado com sucesso!";
            } else {
                $error_message = "Erro ao atualizar conteúdo: " . $stmt->error;
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
    <title>Editar Conteúdo</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleaddconteudo.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Editar Conteúdo</h1>

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
                <label for="conteudo">Selecione o Conteúdo para Editar:</label>
                <select name="conteudo_id" id="conteudo" required onchange="this.form.submit()">
                    <option value="">Escolha um conteúdo</option>
                    <?php foreach ($conteudos as $conteudo): ?>
                        <option value="<?php echo $conteudo['id']; ?>" <?php echo isset($_POST['conteudo_id']) && $_POST['conteudo_id'] == $conteudo['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($conteudo['titulo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <?php if ($conteudo_atual): ?>
                <label for="titulo">Título do Conteúdo:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($conteudo_atual['titulo']); ?>" required>

                <label for="material">Material:</label>
                <textarea id="material" name="material" rows="5" required><?php echo htmlspecialchars($conteudo_atual['material']); ?></textarea>

                <input type="hidden" name="conteudo_id" value="<?php echo $conteudo_atual['id']; ?>">

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
