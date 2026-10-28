<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['usuario_id'])) {
    header("location: painel.php");
    exit();
}

$usuario = "";
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if ($usuario === "" || $senha === "") {
        $erro = "Informe usuário e senha.";
    } else {
        $sql = "select id, nome, senha from usuarios where usuario = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $usuario);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $nome, $senha_banco);
                mysqli_stmt_fetch($stmt);

                // Aceita senha com hash (recomendado) ou texto puro (script da aula)
                if (password_verify($senha, $senha_banco) || hash_equals($senha_banco, $senha)) {
                    session_regenerate_id(true);
                    $_SESSION['usuario_id'] = $id;
                    $_SESSION['usuario_nome'] = $nome;
                    header("location: painel.php");
                    exit();
                }
            }
            // Mesma mensagem para usuário inexistente ou senha errada
            $erro = "Usuário ou senha inválidos.";
            mysqli_stmt_close($stmt);
        } else {
            $erro = "Erro interno. Tente novamente mais tarde.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Cafeteria</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <style type="text/css">
        .wrapper { width: 380px; margin: 20px auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="page-header"><h2>Gestão da Cafeteria</h2></div>
        <p>Entre com seu usuário e senha.</p>
        <?php if ($erro !== ""): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Usuário</label>
                <input type="text" name="usuario" class="form-control" value="<?php echo htmlspecialchars($usuario); ?>">
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" class="form-control">
            </div>
            <input type="submit" class="btn btn-primary" value="Entrar">
        </form>
    </div>
</body>
</html>
