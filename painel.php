<?php
require_once 'verifica_login.php';
require_once 'config.php';

$nome_item = $categoria = $descricao = $preco = "";
$nome_err = $categoria_err = $preco_err = "";
$sucesso = "";

// ---------- CREATE ----------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $sql = "insert into cardapio (nome_item, categoria, descricao, preco, disponivel) values (?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            $preco_final = (float) $preco_num;
            mysqli_stmt_bind_param($stmt, "sssdi", $nome_item, $categoria, $descricao, $preco_final, $disponivel);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("location: painel.php?ok=1"); // evita reenvio ao atualizar a página
                exit();
            } else {
                $sucesso = "";
                echo "Algo deu errado. Tente novamente mais tarde.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

if (isset($_GET["ok"])) { $sucesso = "Item cadastrado com sucesso!"; }
if (isset($_GET["editado"])) { $sucesso = "Item atualizado com sucesso!"; }
if (isset($_GET["excluido"])) { $sucesso = "Item excluído com sucesso!"; }

// ---------- READ ----------
$resultado = mysqli_query($link, "select id, nome_item, categoria, descricao, preco, disponivel from cardapio order by id desc");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel - Cafeteria</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <style type="text/css">
        .wrapper { width: 900px; margin: 0 auto; }
        table tr td:last-child { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="page-header clearfix">
                <h2 class="pull-left">Cardápio da Cafeteria</h2>
                <span class="pull-right" style="margin-top:25px;">
                    Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?> |
                    <a href="logout.php">Sair</a>
                </span>
            </div>

            <?php if ($sucesso !== ""): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($sucesso); ?></div>
            <?php endif; ?>

            <h3>Novo item</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row">
                    <div class="col-md-6 form-group <?php echo ($nome_err !== "") ? 'has-error' : ''; ?>">
                        <label>Nome do item</label>
                        <input type="text" name="nome_item" class="form-control" value="<?php echo htmlspecialchars($nome_item); ?>">
                        <span class="help-block"><?php echo $nome_err; ?></span>
                    </div>
                    <div class="col-md-3 form-group <?php echo ($categoria_err !== "") ? 'has-error' : ''; ?>">
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
                    <div class="col-md-3 form-group <?php echo ($preco_err !== "") ? 'has-error' : ''; ?>">
                        <label>Preço (R$)</label>
                        <input type="text" name="preco" class="form-control" value="<?php echo htmlspecialchars($preco); ?>">
                        <span class="help-block"><?php echo $preco_err; ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao" class="form-control" rows="2"><?php echo htmlspecialchars($descricao); ?></textarea>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="disponivel" checked> Disponível</label>
                </div>
                <input type="submit" class="btn btn-primary" value="Cadastrar">
            </form>

            <hr>
            <h3>Itens cadastrados</h3>
            <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th><th>Nome</th><th>Categoria</th><th>Descrição</th>
                            <th>Preço</th><th>Disponível</th><th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?php echo (int) $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nome_item']); ?></td>
                            <td><?php echo htmlspecialchars($row['categoria']); ?></td>
                            <td><?php echo htmlspecialchars($row['descricao'] ?? ''); ?></td>
                            <td>R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></td>
                            <td><?php echo $row['disponivel'] ? 'Sim' : 'Não'; ?></td>
                            <td>
                                <a href="editar_item.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span> Editar</a>
                                <a href="excluir_item.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span> Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted"><em>Nenhum item cadastrado ainda.</em></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($link); ?>
