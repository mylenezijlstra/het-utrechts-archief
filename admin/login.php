<?php
// admin/login.php - CMS Inloggen
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

$error = '';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        $error = 'Ongeldige sessie of CSRF-token. Probeer het opnieuw.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $error = 'Vul zowel uw gebruikersnaam als wachtwoord in.';
        } else {
            $db = getDB();
            $stmt = $db->prepare("SELECT id, username, password_hash, role FROM users WHERE username = :u LIMIT 1");
            $stmt->execute(['u' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['hua_user_id'] = $user['id'];
                $_SESSION['hua_username'] = $user['username'];
                $_SESSION['hua_role'] = $user['role'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Onjuiste gebruikersnaam of wachtwoord.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen CMS – Het Utrechts Archief</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <header class="admin-header">
        <a href="../index.php" class="admin-brand">
            <?= renderHuaLogoSvg('white') ?>
            <span style="font-size: 1.1rem; font-weight: bold; margin-left: 8px;">CMS Beheeromgeving</span>
        </a>
        <a href="../index.php" class="btn-admin btn-admin-header">
            ⬅️ Terug naar website
        </a>
    </header>

    <div class="login-wrap">
        <div class="login-card">
            <div class="login-logo">
                <div style="width: 180px; margin: 0 auto 12px;">
                    <?= renderHuaLogoSvg('positive') ?>
                </div>
                <h1 style="color: var(--hua-purple); font-size: 1.3rem;">Inloggen Beheersysteem</h1>
                <p style="font-size: 0.85rem; color: #666; margin-top: 4px;">Ontmoet de Oudegracht • Het Utrechts Archief</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <input type="hidden" name="csrf_token" value="<?= e(getCsrfToken()) ?>">

                <div class="form-group">
                    <label for="username">Gebruikersnaam</label>
                    <input type="text" id="username" name="username" class="form-control" required autofocus value="admin">
                </div>

                <div class="form-group">
                    <label for="password">Wachtwoord</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                    <small style="color: #666; margin-top: 4px;">Standaard inlog: <code>admin</code> / <code>admin123</code></small>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; justify-content: center; padding: 12px; font-size: 1rem;">
                        🔐 Inloggen
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
