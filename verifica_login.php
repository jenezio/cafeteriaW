<?php
// Inclua este arquivo no topo de toda página protegida
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("location: login.php");
    exit();
}
