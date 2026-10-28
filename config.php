<?php
// Configuração da conexão (padrão XAMPP)
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '@Unifan123');
define('DB_NAME', 'gestao_cafeteria');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERRO: não foi possível conectar. " . mysqli_connect_error());
}
mysqli_set_charset($link, "utf8mb4");

// Categorias permitidas (usadas no <select> e na validação)
$categorias = ['Café', 'Doce', 'Salgado', 'Bebida Gelada'];
