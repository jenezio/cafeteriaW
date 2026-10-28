<?php
require_once 'verifica_login.php';
require_once 'config.php';

$nome_err = $categoria_err = $preco_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // O id vem de um campo hidden: é ele que garante UPDATE e não INSERT
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    if (!$id) { header("location: erro.php"); exit(); }

    $nome_item = trim($_POST["nome_item"] ?? "");
    $categoria = $_POST["categoria"] ?? "";
    $descricao = trim($_POST["descricao"] ?? "");
    $preco = trim($_POST["preco"] ?? "");
    $disponivel = isset($_POST["disponivel"]) ? 1 : 0;

    if ($nome_item === "") {
        $nome_err = "Informe o nome do item.";
    } elseif (mb_strlen($nome_item) > 100) {
        $nome_err = "O nome pode ter no máximo 100 caracteres.";
    }

    if (!in_array($categoria, $categorias, true)) {
        $categoria_err = "Escolha uma categoria válida.";
    }

    $preco_num = str_replace(',', '.', $preco);
    if ($preco === "") {
        $preco_err = "Informe o preço.";
    } elseif (!is_numeric($preco_num) || $preco_num < 0) {
        $preco_err = "Informe um preço válido (ex: 7.50).";
    }

    if ($nome_err === "" && $categoria_err === "" && $preco_err === "") {
        $sql = "update cardapio set nome_item = ?, categoria = ?, descricao = ?, preco = ?, disponivel = ? where id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            $preco_final = (float) $preco_num;
            mysqli_stmt_bind_param($stmt, "sssdii", $nome_item, $categoria, $descricao, $preco_final, $disponivel, $id);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("location: painel.php?editado=1");
                exit();
            } else {
                echo "Algo deu errado. Tente novamente mais tarde.";
            }
            mysqli_stmt_close($stmt);
        }
    }
} else {
    // Primeiro acesso: busca o item para preencher o formulário
    $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
    if (!$id) { header("location: erro.php"); exit(); }

    $sql = "select nome_item, categoria, descricao, preco, disponivel from cardapio where id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);

        if (!$row) { header("location: erro.php"); exit(); }

        $nome_item = $row["nome_item"];
        $categoria = $row["categoria"];
        $descricao = $row["descricao"] ?? "";
        $preco = number_format($row["preco"], 2, '.', '');
        $disponivel = (int) $row["disponivel"];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar item - Cafeteria</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <style type="text/css">
        .wrapper { width: 500px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="page-header"><h2>Editar item</h2></div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo ($nome_err !== "") ? 'has-error' : ''; ?>">
                    <label>Nome do item</label>
                    <input type="text" name="nome_item" class="form-control" value="<?php echo htmlspecialchars($nome_item); ?>">
                    <span class="help-block"><?php echo $nome_err; ?></span>
                </div>
                <div class="form-group <?php echo ($categoria_err !== "") ? 'has-error' : ''; ?>">
                    <label>Categoria</label>
                    <select name="categoria" class="form-control">
                        <option value="">Selecione...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($categoria === $cat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="help-block"><?php echo $categoria_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao" class="form-control" rows="3"><?php echo htmlspecialchars($descricao); ?></textarea>
                </div>
                <div class="form-group <?php echo ($preco_err !== "") ? 'has-error' : ''; ?>">
                    <label>Preço (R$)</label>
                    <input type="text" name="preco" class="form-control" value="<?php echo htmlspecialchars($preco); ?>">
                    <span class="help-block"><?php echo $preco_err; ?></span>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="disponivel" <?php echo !empty($disponivel) ? 'checked' : ''; ?>> Disponível</label>
                </div>
                <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
                <input type="submit" class="btn btn-primary" value="Salvar">
                <a href="painel.php" class="btn btn-default">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($link); ?>
