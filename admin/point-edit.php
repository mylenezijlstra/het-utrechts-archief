<?php
// admin/point-edit.php - Routepunt toevoegen of bewerken
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();
$db = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = ($id > 0);
$error = '';

// Default values
$point = [
    'code' => '',
    'sort_order' => 10,
    'video_start_sec' => 0,
    'video_end_sec' => 15,
    'latitude' => 52.0912,
    'longitude' => 5.1200,
    'is_bridge' => 0,
    'is_active' => 1,
    'title_nl' => '',
    'desc_nl' => '',
    'title_en' => '',
    'desc_en' => ''
];
$mediaItems = [];

if ($isEdit) {
    $stmt = $db->prepare("SELECT * FROM route_points WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $existing = $stmt->fetch();
    if (!$existing) {
        header('Location: index.php');
        exit;
    }
    $point = array_merge($point, $existing);

    // Fetch translations
    $stmt = $db->prepare("SELECT lang_code, title, description FROM route_translations WHERE route_point_id = :id");
    $stmt->execute(['id' => $id]);
    while ($row = $stmt->fetch()) {
        if ($row['lang_code'] === 'nl') {
            $point['title_nl'] = $row['title'];
            $point['desc_nl'] = $row['description'];
        } elseif ($row['lang_code'] === 'en') {
            $point['title_en'] = $row['title'];
            $point['desc_en'] = $row['description'];
        }
    }

    // Fetch media items
    $stmt = $db->prepare("SELECT * FROM media_items WHERE route_point_id = :id ORDER BY sort_order ASC, id ASC");
    $stmt->execute(['id' => $id]);
    $mediaItems = $stmt->fetchAll();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Ongeldige sessie of CSRF-token.';
    } else {
        $code = trim($_POST['code'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $startSec = (int)($_POST['video_start_sec'] ?? 0);
        $endSec = (int)($_POST['video_end_sec'] ?? 0);
        $lat = (float)($_POST['latitude'] ?? 52.0912);
        $lng = (float)($_POST['longitude'] ?? 5.1200);
        $isBridge = !empty($_POST['is_bridge']) ? 1 : 0;
        $isActive = !empty($_POST['is_active']) ? 1 : 0;

        $titleNl = trim($_POST['title_nl'] ?? '');
        $descNl = trim($_POST['desc_nl'] ?? '');
        $titleEn = trim($_POST['title_en'] ?? '') ?: $titleNl;
        $descEn = trim($_POST['desc_en'] ?? '') ?: $descNl;

        if (empty($code) || empty($titleNl)) {
            $error = 'Vul minimaal een routecode (bijv. 1 of 1a) en een Nederlandse titel in.';
        } else {
            if ($isEdit) {
                $stmt = $db->prepare("
                    UPDATE route_points 
                    SET code = :code, sort_order = :sort_order, video_start_sec = :start, 
                        video_end_sec = :end, latitude = :lat, longitude = :lng, 
                        is_bridge = :is_bridge, is_active = :is_active
                    WHERE id = :id
                ");
                $stmt->execute([
                    'code' => $code,
                    'sort_order' => $sortOrder,
                    'start' => $startSec,
                    'end' => $endSec,
                    'lat' => $lat,
                    'lng' => $lng,
                    'is_bridge' => $isBridge,
                    'is_active' => $isActive,
                    'id' => $id
                ]);

                // Update translations
                $stmt = $db->prepare("DELETE FROM route_translations WHERE route_point_id = :id");
                $stmt->execute(['id' => $id]);

                $stmt = $db->prepare("INSERT INTO route_translations (route_point_id, lang_code, title, description) VALUES (?, ?, ?, ?)");
                $stmt->execute([$id, 'nl', $titleNl, $descNl]);
                $stmt->execute([$id, 'en', $titleEn, $descEn]);
                $pointId = $id;
            } else {
                $stmt = $db->prepare("
                    INSERT INTO route_points (code, sort_order, video_start_sec, video_end_sec, latitude, longitude, is_bridge, is_active)
                    VALUES (:code, :sort_order, :start, :end, :lat, :lng, :is_bridge, :is_active)
                ");
                $stmt->execute([
                    'code' => $code,
                    'sort_order' => $sortOrder,
                    'start' => $startSec,
                    'end' => $endSec,
                    'lat' => $lat,
                    'lng' => $lng,
                    'is_bridge' => $isBridge,
                    'is_active' => $isActive
                ]);
                $pointId = $db->lastInsertId();

                $stmt = $db->prepare("INSERT INTO route_translations (route_point_id, lang_code, title, description) VALUES (?, ?, ?, ?)");
                $stmt->execute([$pointId, 'nl', $titleNl, $descNl]);
                $stmt->execute([$pointId, 'en', $titleEn, $descEn]);
            }

            // Handle New Photo Addition (from dropdown or uploaded)
            if (!empty($_POST['add_photo_file'])) {
                $chosenFile = basename($_POST['add_photo_file']);
                $photoPath = 'assets/images/' . $chosenFile;
                $archiveUrl = trim($_POST['add_photo_url'] ?? '');
                $captionNl = trim($_POST['add_photo_caption'] ?? '');
                $altNl = trim($_POST['add_photo_alt'] ?? '') ?: $captionNl;

                $stmt = $db->prepare("
                    INSERT INTO media_items (route_point_id, image_path, archive_url, caption_nl, caption_en, alt_text_nl, alt_text_en, sort_order)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 99)
                ");
                $stmt->execute([$pointId, $photoPath, $archiveUrl, $captionNl, $captionNl, $altNl, $altNl]);
            }

            // Handle Photo Deletion if requested
            if (!empty($_POST['delete_media_id'])) {
                $delMediaId = (int)$_POST['delete_media_id'];
                $stmt = $db->prepare("DELETE FROM media_items WHERE id = ? AND route_point_id = ?");
                $stmt->execute([$delMediaId, $pointId]);
            }

            header("Location: point-edit.php?id=$pointId&msg=saved");
            exit;
        }
    }
}

// Available images in assets/images/
$allImages = glob(__DIR__ . '/../assets/images/*.jpg');
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Routepunt Bewerken' : 'Nieuw Routepunt' ?> – CMS Het Utrechts Archief</title>
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
                <h2><?= $isEdit ? 'Routepunt Bewerken: ' . e($point['title_nl']) : 'Nieuw Routepunt Toevoegen' ?></h2>
                <a href="index.php" class="btn-admin btn-admin-secondary">
                    Terug naar lijst
                </a>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
                <div class="alert alert-success">
                    ✅ De wijzigingen zijn succesvol opgeslagen!
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="point-edit.php<?= $isEdit ? '?id=' . $id : '' ?>">
                <input type="hidden" name="csrf_token" value="<?= e(getCsrfToken()) ?>">

                <div class="form-grid">
                    
                    <!-- Basis Gegevens -->
                    <div class="form-group">
                        <label for="code">Locatiecode (bijv. 1, 1a, 2, 3)</label>
                        <input type="text" id="code" name="code" class="form-control" value="<?= e($point['code']) ?>" required placeholder="Bijv. 1 of 1a">
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Volgorde (oplopend getal)</label>
                        <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= (int)$point['sort_order'] ?>">
                    </div>

                    <!-- Titels -->
                    <div class="form-group">
                        <label for="title_nl">Titel Nederlands (verplicht)</label>
                        <input type="text" id="title_nl" name="title_nl" class="form-control" value="<?= e($point['title_nl']) ?>" required placeholder="Bijv. 2. Maartensbrug">
                    </div>

                    <div class="form-group">
                        <label for="title_en">Titel Engels (optioneel)</label>
                        <input type="text" id="title_en" name="title_en" class="form-control" value="<?= e($point['title_en']) ?>" placeholder="Bijv. 2. Maartensbrug">
                    </div>

                    <!-- Beschrijvingen -->
                    <div class="form-group form-full">
                        <label for="desc_nl">Historische Toelichting (Nederlands)</label>
                        <textarea id="desc_nl" name="desc_nl" class="form-control" rows="4"><?= e($point['desc_nl']) ?></textarea>
                    </div>

                    <div class="form-group form-full">
                        <label for="desc_en">Historische Toelichting (Engels)</label>
                        <textarea id="desc_en" name="desc_en" class="form-control" rows="3"><?= e($point['desc_en']) ?></textarea>
                    </div>

                    <!-- Videotijd -->
                    <div class="form-group">
                        <label for="video_start_sec">Video Starttijd (seconden)</label>
                        <input type="number" id="video_start_sec" name="video_start_sec" class="form-control" value="<?= (int)$point['video_start_sec'] ?>">
                        <small style="color: #666;">Sec: <?= (int)$point['video_start_sec'] ?> (<?= sprintf('%02d:%02d', floor($point['video_start_sec']/60), $point['video_start_sec']%60) ?>)</small>
                    </div>

                    <div class="form-group">
                        <label for="video_end_sec">Video Eindtijd (seconden)</label>
                        <input type="number" id="video_end_sec" name="video_end_sec" class="form-control" value="<?= (int)$point['video_end_sec'] ?>">
                        <small style="color: #666;">Sec: <?= (int)$point['video_end_sec'] ?> (<?= sprintf('%02d:%02d', floor($point['video_end_sec']/60), $point['video_end_sec']%60) ?>)</small>
                    </div>

                    <!-- Coördinaten -->
                    <div class="form-group">
                        <label for="latitude">Breedtegraad (Latitude)</label>
                        <input type="text" id="latitude" name="latitude" class="form-control" value="<?= (float)$point['latitude'] ?>">
                    </div>

                    <div class="form-group">
                        <label for="longitude">Lengtegraad (Longitude)</label>
                        <input type="text" id="longitude" name="longitude" class="form-control" value="<?= (float)$point['longitude'] ?>">
                    </div>

                    <!-- Checkboxes -->
                    <div class="form-group" style="flex-direction: row; align-items: center; gap: 10px; margin-top: 10px;">
                        <input type="checkbox" id="is_bridge" name="is_bridge" value="1" <?= $point['is_bridge'] ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                        <label for="is_bridge" style="cursor: pointer;">🌉 Dit punt is een historische brug (krijgt gouden marker en brugbadge)</label>
                    </div>

                    <div class="form-group" style="flex-direction: row; align-items: center; gap: 10px; margin-top: 10px;">
                        <input type="checkbox" id="is_active" name="is_active" value="1" <?= $point['is_active'] ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                        <label for="is_active" style="cursor: pointer;">✅ Zichtbaar in bezoekersapplicatie (Actief)</label>
                    </div>

                </div>

                <!-- Gekoppelde Archief Foto's -->
                <?php if ($isEdit): ?>
                    <div style="margin-top: 32px; border-top: 2px solid var(--hua-border); padding-top: 24px;">
                        <h3 style="color: var(--hua-purple); margin-bottom: 14px;">Gekoppelde Archiefbeelden (<?= count($mediaItems) ?>)</h3>

                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px;">
                            <?php foreach ($mediaItems as $item): ?>
                                <div style="border: 1px solid var(--hua-border); border-radius: var(--hua-radius); overflow: hidden; background: #fafafa; display: flex; flex-direction: column;">
                                    <div style="height: 150px; background: #000; overflow: hidden;">
                                        <img src="../<?= e($item['image_path']) ?>" alt="<?= e($item['alt_text_nl']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div style="padding: 12px; font-size: 0.85rem; flex: 1;">
                                        <strong><?= e($item['caption_nl'] ?: basename($item['image_path'])) ?></strong><br>
                                        <span style="color: #666; font-size: 0.8rem;">Alt: <?= e($item['alt_text_nl']) ?></span><br>
                                        <?php if ($item['archive_url']): ?>
                                            <a href="<?= e($item['archive_url']) ?>" target="_blank" rel="noopener" style="color: var(--hua-purple); font-size: 0.78rem;">🔗 HUA Beeldbank</a>
                                        <?php endif; ?>
                                    </div>
                                    <div style="padding: 8px 12px; background: #eee; text-align: right;">
                                        <button type="submit" name="delete_media_id" value="<?= (int)$item['id'] ?>" class="btn-admin btn-admin-danger" style="padding: 4px 8px; font-size: 0.75rem;" onclick="return confirm('Afbeelding ontkoppelen van dit routepunt?');">
                                            🗑️ Verwijder foto
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Foto Toevoegen -->
                        <div style="background: #f7f6f2; border: 1px dashed var(--hua-gold); border-radius: var(--hua-radius); padding: 18px;">
                            <h4 style="color: var(--hua-purple); margin-bottom: 10px;">➕ Extra Archieffoto Koppelen</h4>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Kies een gedownloade HUA archieffoto</label>
                                    <select name="add_photo_file" class="form-control">
                                        <option value="">-- Selecteer afbeelding --</option>
                                        <?php foreach ($allImages as $imgPath): ?>
                                            <?php $fn = basename($imgPath); ?>
                                            <option value="<?= e($fn) ?>"><?= e($fn) ?> (<?= round(filesize($imgPath)/1024) ?> KB)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Onderschrift (Nederlands)</label>
                                    <input type="text" name="add_photo_caption" class="form-control" placeholder="Bijv. Gezicht op de Maartensbrug met Dom">
                                </div>
                                <div class="form-group">
                                    <label>Toegankelijke Alt-tekst (voor schermlezers)</label>
                                    <input type="text" name="add_photo_alt" class="form-control" placeholder="Beschrijving wat er op de foto te zien is">
                                </div>
                                <div class="form-group">
                                    <label>Officiële HUA Beeldbank URL (optioneel)</label>
                                    <input type="url" name="add_photo_url" class="form-control" placeholder="https://hetutrechtsarchief.nl/beeld/...">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <a href="index.php" class="btn-admin btn-admin-secondary">Annuleren</a>
                    <button type="submit" class="btn-admin btn-admin-primary">
                        💾 Wijzigingen Opslaan
                    </button>
                </div>
            </form>
        </div>

    </main>

</body>
</html>
