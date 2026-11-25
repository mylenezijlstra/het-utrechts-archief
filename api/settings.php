<?php
// api/settings.php - Returns application settings (colofon, intro) as JSON
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

try {
    $db = getDB();
    $stmt = $db->query("SELECT setting_key, value_nl, value_en FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = [
            'nl' => $row['value_nl'],
            'en' => $row['value_en']
        ];
    }

    jsonResponse([
        'status' => 'success',
        'data' => $settings
    ]);
} catch (Exception $e) {
    jsonResponse([
        'status' => 'error',
        'message' => $e->getMessage()
    ], 500);
}
