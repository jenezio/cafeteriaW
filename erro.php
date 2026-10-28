<?php require_once 'verifica_login.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Erro</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <style type="text/css">
        .wrapper { width: 750px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="page-header"><h1>Requisição inválida</h1></div>
            <div class="alert alert-danger fade in">
                <p>O item solicitado não existe ou o pedido é inválido.
                   <a href="painel.php" class="alert-link">Voltar ao painel</a>.</p>
            </div>
        </div>
    </div>
</body>
</html>
