<?php
// admin/point-delete.php - Routepunt veilig verwijderen
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = $_GET['token'] ?? '';

if ($id > 0 && verifyCsrfToken($token)) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM route_points WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header('Location: index.php?msg=deleted');
    exit;
}

header('Location: index.php');
exit;
