<?php
// admin/index.php - CMS Dashboard
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();
$user = getCurrentUser();
$db = getDB();

// Fetch points with translation and image count
$stmt = $db->query("
    SELECT 
        p.id, p.code, p.sort_order, p.video_start_sec, p.video_end_sec, 
        p.latitude, p.longitude, p.is_bridge, p.is_active,
        t.title AS title_nl,
        COUNT(m.id) AS media_count
    FROM route_points p
    LEFT JOIN route_translations t ON t.route_point_id = p.id AND t.lang_code = 'nl'
    LEFT JOIN media_items m ON m.route_point_id = p.id
    GROUP BY p.id
    ORDER BY p.sort_order ASC, p.video_start_sec ASC
");
$points = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Dashboard – Het Utrechts Archief</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <header class="admin-header">
        <a href="index.php" class="admin-brand">
            <?= renderHuaLogoSvg('white') ?>
            <span style="font-size: 1.1rem; font-weight: bold; margin-left: 8px;">CMS Beheersysteem</span>
        </a>

        <div class="admin-nav">
            <span style="color: rgba(255,255,255,0.85); font-size: 0.9rem;">
                Ingelogd als: <strong><?= e($user['username']) ?></strong>
            </span>
            <a href="settings.php" class="btn-admin btn-admin-header">
                📝 Teksten & Colofon
            </a>
            <a href="../index.php" target="_blank" class="btn-admin btn-admin-header">
                👁️ Bekijk Website
            </a>
            <a href="logout.php" class="btn-admin btn-admin-danger">
                🚪 Uitloggen
            </a>
        </div>
    </header>

    <main class="admin-container">
        
        <div class="admin-card">
            <div class="admin-card-header">
                <div>
                    <h2>Routepunten Oudegracht Beheren</h2>
                    <p style="color: #666; font-size: 0.9rem; margin-top: 4px;">
                        Beheer de historische locaties, tijdscodes in de 1929-film, coördinaten en gekoppelde archieffoto's.
                    </p>
                </div>
                <a href="point-edit.php" class="btn-admin btn-admin-primary">
                    ➕ Nieuw Routepunt Toevoegen
                </a>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
                <div class="alert alert-success">
                    ✅ Routepunt succesvol opgeslagen! De wijzigingen zijn direct zichtbaar in de webapplicatie.
                </div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                <div class="alert alert-success">
                    🗑️ Routepunt succesvol verwijderd.
                </div>
            <?php endif; ?>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Code</th>
                        <th>Titel (NL)</th>
                        <th>Type</th>
                        <th>Videotijd</th>
                        <th>Coördinaten</th>
                        <th>Foto's</th>
                        <th>Status</th>
                        <th style="width: 180px; text-align: right;">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($points)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #888; padding: 24px;">
                                Geen routepunten gevonden. Klik op "Nieuw Routepunt Toevoegen" om te beginnen.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($points as $pt): ?>
                            <tr>
                                <td><strong><?= e($pt['code']) ?></strong></td>
                                <td>
                                    <strong><?= e($pt['title_nl'] ?: 'Zonder titel') ?></strong>
                                </td>
                                <td>
                                    <?php if ($pt['is_bridge']): ?>
                                        <span class="badge-tag badge-bridge">🌉 Brug</span>
                                    <?php else: ?>
                                        <span class="badge-tag badge-wharf">🏛️ Werf / Handel</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <code><?= sprintf('%02d:%02d', floor($pt['video_start_sec']/60), $pt['video_start_sec']%60) ?> - <?= sprintf('%02d:%02d', floor($pt['video_end_sec']/60), $pt['video_end_sec']%60) ?></code>
                                </td>
                                <td style="font-size: 0.85rem; color: #666;">
                                    <?= round($pt['latitude'], 4) ?>, <?= round($pt['longitude'], 4) ?>
                                </td>
                                <td>
                                    <strong><?= (int)$pt['media_count'] ?></strong> archieffoto('s)
                                </td>
                                <td>
                                    <?php if ($pt['is_active']): ?>
                                        <span style="color: #28a745; font-weight: bold;">● Actief</span>
                                    <?php else: ?>
                                        <span style="color: #dc3545; font-weight: bold;">○ Inactief</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <a href="point-edit.php?id=<?= (int)$pt['id'] ?>" class="btn-admin btn-admin-secondary" style="padding: 6px 10px; font-size: 0.82rem;">
                                        ✏️ Bewerken
                                    </a>
                                    <a href="point-delete.php?id=<?= (int)$pt['id'] ?>&token=<?= e(getCsrfToken()) ?>" class="btn-admin btn-admin-danger" style="padding: 6px 10px; font-size: 0.82rem;" onclick="return confirm('Weet u zeker dat u dit routepunt wilt verwijderen?');">
                                        🗑️ Wis
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>

</body>
</html>
