<?php
// api/points.php - Returns all active route points, translations and media items as JSON
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

try {
    $db = getDB();
    
    // Fetch route points
    $stmt = $db->query("
        SELECT id, code, sort_order, video_start_sec, video_end_sec, latitude, longitude, is_bridge, is_active
        FROM route_points
        WHERE is_active = 1
        ORDER BY sort_order ASC, video_start_sec ASC
    ");
    $points = $stmt->fetchAll();

    // Fetch translations
    $stmt = $db->query("
        SELECT route_point_id, lang_code, title, description
        FROM route_translations
    ");
    $translations = [];
    while ($row = $stmt->fetch()) {
        $ptId = $row['route_point_id'];
        $lang = $row['lang_code'];
        if (!isset($translations[$ptId])) {
            $translations[$ptId] = [];
        }
        $translations[$ptId][$lang] = [
            'title' => $row['title'],
            'description' => $row['description']
        ];
    }

    // Fetch media items
    $stmt = $db->query("
        SELECT id, route_point_id, image_path, archive_url, caption_nl, caption_en, alt_text_nl, alt_text_en, sort_order
        FROM media_items
        ORDER BY sort_order ASC, id ASC
    ");
    $media = [];
    while ($row = $stmt->fetch()) {
        $ptId = $row['route_point_id'];
        if (!isset($media[$ptId])) {
            $media[$ptId] = [];
        }
        $media[$ptId][] = [
            'id' => (int)$row['id'],
            'image_path' => $row['image_path'],
            'archive_url' => $row['archive_url'],
            'caption_nl' => $row['caption_nl'],
            'caption_en' => $row['caption_en'],
            'alt_text_nl' => $row['alt_text_nl'],
            'alt_text_en' => $row['alt_text_en']
        ];
    }

    // Assemble unified structure
    $result = [];
    foreach ($points as $p) {
        $id = (int)$p['id'];
        $trans = $translations[$id] ?? [];
        $result[] = [
            'id' => $id,
            'code' => $p['code'],
            'sort_order' => (int)$p['sort_order'],
            'video_start_sec' => (int)$p['video_start_sec'],
            'video_end_sec' => (int)$p['video_end_sec'],
            'latitude' => (float)$p['latitude'],
            'longitude' => (float)$p['longitude'],
            'is_bridge' => (bool)$p['is_bridge'],
            'title_nl' => $trans['nl']['title'] ?? $p['code'],
            'description_nl' => $trans['nl']['description'] ?? '',
            'title_en' => $trans['en']['title'] ?? ($trans['nl']['title'] ?? $p['code']),
            'description_en' => $trans['en']['description'] ?? ($trans['nl']['description'] ?? ''),
            'images' => $media[$id] ?? []
        ];
    }

    jsonResponse([
        'status' => 'success',
        'count' => count($result),
        'data' => $result
    ]);
} catch (Exception $e) {
    jsonResponse([
        'status' => 'error',
        'message' => $e->getMessage()
    ], 500);
}
