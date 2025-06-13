<?php
session_start();

$action = $_POST['action'] ?? '';

if ($action === 'update') {
    $_SESSION['panier'] = json_decode($_POST['panier'], true);
}
?>
