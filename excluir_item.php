<?php
require_once 'verifica_login.php';
require_once 'config.php';

// Passo 2: confirmação enviada (POST) -> exclui
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    if (!$id) { header("location: erro.php"); exit(); }

    $sql = "delete from cardapio where id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("location: painel.php?excluido=1");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
    echo "Algo deu errado. Tente novamente mais tarde.";
    exit();
}

// Passo 1: chegou pelo link ?id=X -> mostra a confirmação
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) { header("location: erro.php"); exit(); }

$nome_item = "";
$sql = "select nome_item from cardapio where id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    if (!$row) { header("location: erro.php"); exit(); }
    $nome_item = $row["nome_item"];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir item - Cafeteria</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="estilo.css">
    <style type="text/css">
        .wrapper { width: 500px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="page-header"><h1>Excluir item</h1></div>
            <form action="excluir_item.php" method="post">
                <div class="alert alert-danger fade in">
                    <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
                    <p>Tem certeza que deseja excluir <strong><?php echo htmlspecialchars($nome_item); ?></strong>?</p><br>
                    <p>
                        <input type="submit" value="Sim, excluir" class="btn btn-danger">
                        <a href="painel.php" class="btn btn-default">Não</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
