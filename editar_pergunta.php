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

$sql_perguntas = "SELECT id, pergunta FROM perguntas"; 
$result_perguntas = $conexao->query($sql_perguntas);

if ($result_perguntas->num_rows > 0) {
    $perguntas = $result_perguntas->fetch_all(MYSQLI_ASSOC);
} else {
    $perguntas = [];
}

$pergunta_atual = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['pergunta_id']) && !empty($_POST['pergunta_id'])) {
        $pergunta_id = $_POST['pergunta_id'];

        $sql_pergunta = "SELECT id, pergunta, alternativa_a, alternativa_b, alternativa_c, alternativa_d, resposta_correta 
                         FROM perguntas WHERE id = ?";
        $stmt = $conexao->prepare($sql_pergunta);
        $stmt->bind_param("i", $pergunta_id);
        $stmt->execute();
        $result_pergunta = $stmt->get_result();
        if ($result_pergunta->num_rows > 0) {
            $pergunta_atual = $result_pergunta->fetch_assoc();
        }
        $stmt->close();
    }

    if (isset($_POST['pergunta']) && isset($_POST['alternativa_a']) && isset($_POST['alternativa_b']) &&
        isset($_POST['alternativa_c']) && isset($_POST['alternativa_d']) && isset($_POST['resposta_correta']) && isset($_POST['pergunta_id'])) {
        
        $pergunta_texto = $_POST['pergunta'];
        $alternativa_a = $_POST['alternativa_a'];
        $alternativa_b = $_POST['alternativa_b'];
        $alternativa_c = $_POST['alternativa_c'];
        $alternativa_d = $_POST['alternativa_d'];
        $resposta_correta = $_POST['resposta_correta'];
        $pergunta_id = $_POST['pergunta_id'];

        if (empty($pergunta_texto) || empty($alternativa_a) || empty($alternativa_b) || 
            empty($alternativa_c) || empty($alternativa_d) || empty($resposta_correta)) {
            $error_message = "Por favor, preencha todos os campos.";
        } else {
            $sql_update = "UPDATE perguntas 
                           SET pergunta = ?, alternativa_a = ?, alternativa_b = ?, alternativa_c = ?, alternativa_d = ?, resposta_correta = ?
                           WHERE id = ?";
            $stmt_update = $conexao->prepare($sql_update);
            $stmt_update->bind_param("ssssssii", $pergunta_texto, $alternativa_a, $alternativa_b, $alternativa_c, $alternativa_d, $resposta_correta, $pergunta_id);

            if ($stmt_update->execute()) {
                $success_message = "Pergunta atualizada com sucesso!";
            } else {
                $error_message = "Erro ao atualizar pergunta: " . $stmt_update->error;
            }

            $stmt_update->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pergunta</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleaddconteudo.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Editar Pergunta</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="pergunta">Selecione a Pergunta</label>
            <select name="pergunta_id" id="pergunta" required onchange="this.form.submit()">
                <option value="">Escolha uma pergunta</option> 
                <?php foreach ($perguntas as $pergunta): ?>
                    <option value="<?php echo $pergunta['id']; ?>" <?php echo isset($_POST['pergunta_id']) && $_POST['pergunta_id'] == $pergunta['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($pergunta['pergunta']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (isset($_POST['pergunta_id']) && !empty($_POST['pergunta_id'])): ?>
                <?php if ($pergunta_atual): ?>
                    <label for="pergunta">Pergunta:</label>
                    <textarea id="pergunta" name="pergunta" rows="5" required><?php echo htmlspecialchars($pergunta_atual['pergunta']); ?></textarea>

                    <label for="alternativa_a">Alternativa A:</label>
                    <input type="text" id="alternativa_a" name="alternativa_a" value="<?php echo htmlspecialchars($pergunta_atual['alternativa_a']); ?>" required>

                    <label for="alternativa_b">Alternativa B:</label>
                    <input type="text" id="alternativa_b" name="alternativa_b" value="<?php echo htmlspecialchars($pergunta_atual['alternativa_b']); ?>" required>

                    <label for="alternativa_c">Alternativa C:</label>
                    <input type="text" id="alternativa_c" name="alternativa_c" value="<?php echo htmlspecialchars($pergunta_atual['alternativa_c']); ?>" required>

                    <label for="alternativa_d">Alternativa D:</label>
                    <input type="text" id="alternativa_d" name="alternativa_d" value="<?php echo htmlspecialchars($pergunta_atual['alternativa_d']); ?>" required>

                    <label for="resposta_correta">Resposta Correta:</label>
                    <select name="resposta_correta" id="resposta_correta" required>
                        <option value="a" <?php echo ($pergunta_atual['resposta_correta'] == 'a') ? 'selected' : ''; ?>>Alternativa A</option>
                        <option value="b" <?php echo ($pergunta_atual['resposta_correta'] == 'b') ? 'selected' : ''; ?>>Alternativa B</option>
                        <option value="c" <?php echo ($pergunta_atual['resposta_correta'] == 'c') ? 'selected' : ''; ?>>Alternativa C</option>
                        <option value="d" <?php echo ($pergunta_atual['resposta_correta'] == 'd') ? 'selected' : ''; ?>>Alternativa D</option>
                    </select>

                    <input type="hidden" name="pergunta_id" value="<?php echo $pergunta_atual['id']; ?>">
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
