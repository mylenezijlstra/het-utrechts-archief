<?php
// admin/settings.php - Algemene teksten en colofon beheren
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();
$db = getDB();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Ongeldig CSRF-token.';
    } else {
        $colofonNl = trim($_POST['colofon_nl'] ?? '');
        $colofonEn = trim($_POST['colofon_en'] ?? '');
        $introNl = trim($_POST['intro_nl'] ?? '');
        $introEn = trim($_POST['intro_en'] ?? '');

        $stmt = $db->prepare("UPDATE settings SET value_nl = ?, value_en = ? WHERE setting_key = ?");
        $stmt->execute([$colofonNl, $colofonEn, 'colofon']);
        $stmt->execute([$introNl, $introEn, 'intro']);
        $success = 'Instellingen en teksten succesvol opgeslagen!';
    }
}

// Fetch current settings
$stmt = $db->query("SELECT setting_key, value_nl, value_en FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teksten & Colofon Beheren – CMS Het Utrechts Archief</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <header class="admin-header">
        <a href="index.php" class="admin-brand">
            <?= renderHuaLogoSvg('white') ?>
            <span style="font-size: 1.1rem; font-weight: bold; margin-left: 8px;">CMS Beheersysteem</span>
        </a>

        <div class="admin-nav">
            <a href="index.php" class="btn-admin btn-admin-header">
                ⬅️ Overzicht Routepunten
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
                <h2>Algemene Informatie, Start-uitleg & Colofon</h2>
                <a href="index.php" class="btn-admin btn-admin-secondary">
                    Terug naar dashboard
                </a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= e($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="settings.php">
                <input type="hidden" name="csrf_token" value="<?= e(getCsrfToken()) ?>">

                <h3 style="color: var(--hua-purple); margin-bottom: 8px;">1. Start-uitleg Pop-up (bij openen van de applicatie)</h3>
                <div class="form-group">
                    <label for="intro_nl">Uitlegtekst Nederlands</label>
                    <textarea id="intro_nl" name="intro_nl" class="form-control" rows="3"><?= e($settings['intro']['value_nl'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="intro_en">Uitlegtekst Engels</label>
                    <textarea id="intro_en" name="intro_en" class="form-control" rows="3"><?= e($settings['intro']['value_en'] ?? '') ?></textarea>
                </div>

                <hr style="margin: 28px 0; border: none; border-top: 1px solid var(--hua-border);">

                <h3 style="color: var(--hua-purple); margin-bottom: 8px;">2. Colofon Informatie (organisatie, makers, tentoonstelling)</h3>
                <div class="form-group">
                    <label for="colofon_nl">Colofontekst Nederlands</label>
                    <textarea id="colofon_nl" name="colofon_nl" class="form-control" rows="6"><?= e($settings['colofon']['value_nl'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="colofon_en">Colofontekst Engels</label>
                    <textarea id="colofon_en" name="colofon_en" class="form-control" rows="6"><?= e($settings['colofon']['value_en'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-admin btn-admin-primary">
                        💾 Wijzigingen Opslaan
                    </button>
                </div>
            </form>
        </div>

    </main>

</body>
</html>
