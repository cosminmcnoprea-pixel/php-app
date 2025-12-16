<?php
// Simple starter script to confirm PHP + Nginx + Cloud Run integration.
require_once __DIR__ . '/../bootstrap.php';

use App\Service\AppConfig;
use App\Service\SystemStatusService;

$envValues = array_merge($_ENV ?? [], $_SERVER ?? []);
$config = AppConfig::fromGlobals($envValues);
$statusService = new SystemStatusService($config);
$status = $statusService->summary();
$warnings = $statusService->warnings();
$staticBucketUrl = $config->getStaticBucketUrl();

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
    <p><strong>Health:</strong> <?php echo htmlspecialchars($status['health'], ENT_QUOTES, 'UTF-8'); ?></p>
    <ul>
        <li>Project ID: <code><?php echo htmlspecialchars($status['projectId'], ENT_QUOTES, 'UTF-8'); ?></code></li>
        <li>Environment: <strong><?php echo htmlspecialchars($status['environment'], ENT_QUOTES, 'UTF-8'); ?></strong></li>
        <li>DB Host: <code><?php echo htmlspecialchars($status['dbHost'] ?? $config->getDbHost(), ENT_QUOTES, 'UTF-8'); ?></code></li>
        <li>DB Name: <code><?php echo htmlspecialchars($status['dbName'] ?? $config->getDbName(), ENT_QUOTES, 'UTF-8'); ?></code></li>
        <li>DB User: <code><?php echo htmlspecialchars($status['dbUser'] ?? $config->getDbUser(), ENT_QUOTES, 'UTF-8'); ?></code></li>
        <li>Static Bucket: <code><?php echo htmlspecialchars($staticBucketUrl ?: 'not-configured', ENT_QUOTES, 'UTF-8'); ?></code></li>
        <li>Database DSN: <code><?php echo htmlspecialchars($status['databaseDsn'] ?: 'not-configured', ENT_QUOTES, 'UTF-8'); ?></code></li>
        <li>PHP Version: <code><?php echo htmlspecialchars($status['phpVersion'], ENT_QUOTES, 'UTF-8'); ?></code></li>
        <li>Client Time: <span id="timestamp">Loading...</span></li>
    </ul>
    <?php if (!empty($status['featureFlags'])): ?>
        <h3>Feature Flags</h3>
        <ul>
            <?php foreach ($status['featureFlags'] as $flag): ?>
                <li><?php echo htmlspecialchars($flag, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($warnings)): ?>
        <h3>Warnings</h3>
        <ul>
            <?php foreach ($warnings as $warning): ?>
                <li><?php echo htmlspecialchars($warning, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<?php if ($staticBucketUrl): ?>
<script src="<?php echo htmlspecialchars($staticBucketUrl, ENT_QUOTES, 'UTF-8'); ?>/js/app.js"></script>
<?php endif; ?>
</body>
</html>
