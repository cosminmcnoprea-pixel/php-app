<?php
// Simple starter script to confirm PHP + Nginx + Cloud Run integration.

$env = getenv('APP_ENV') ?: 'local';
$dbHost = getenv('DB_HOST') ?: 'not-configured';
$dbName = getenv('DB_NAME') ?: 'not-configured';
$dbUser = getenv('DB_USER') ?: 'not-configured';
$staticBucketUrl = getenv('STATIC_BUCKET_URL') ?: '';

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP-FPM on Cloud Run</title>
    <?php if ($staticBucketUrl): ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($staticBucketUrl, ENT_QUOTES, 'UTF-8'); ?>/css/style.css">
    <?php endif; ?>
</head>
<body>
<div class="card">
    <h1>PHP-FPM on Cloud Run</h1>
    <p>This is a minimal PHP application served by Nginx and PHP-FPM.</p>
    <ul>
        <li>Environment: <strong><?php echo htmlspecialchars($env, ENT_QUOTES, 'UTF-8'); ?></strong></li>
        <li>DB Host: <code><?php echo htmlspecialchars($dbHost, ENT_QUOTES, 'UTF-8'); ?></code></li>
        <li>DB Name: <code><?php echo htmlspecialchars($dbName, ENT_QUOTES, 'UTF-8'); ?></code></li>
        <li>DB User: <code><?php echo htmlspecialchars($dbUser, ENT_QUOTES, 'UTF-8'); ?></code></li>
        <li>Static Bucket: <code><?php echo htmlspecialchars($staticBucketUrl ?: 'not-configured', ENT_QUOTES, 'UTF-8'); ?></code></li>
        <li>Client Time: <span id="timestamp">Loading...</span></li>
    </ul>
</div>
<?php if ($staticBucketUrl): ?>
<script src="<?php echo htmlspecialchars($staticBucketUrl, ENT_QUOTES, 'UTF-8'); ?>/js/app.js"></script>
<?php endif; ?>
</body>
</html>
