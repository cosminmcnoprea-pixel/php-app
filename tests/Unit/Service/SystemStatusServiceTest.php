<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\AppConfig;
use App\Service\SystemStatusService;
use PHPUnit\Framework\TestCase;

class SystemStatusServiceTest extends TestCase
{
    public function testHealthIsDegradedWhenConfigMissing(): void
    {
        $config = new AppConfig('demo', 'dev', '', '', '', '', []);
        $service = new SystemStatusService($config);

        $summary = $service->summary();
        $warnings = $service->warnings();

        $this->assertSame('degraded', $summary['health']);
        $this->assertContains('Missing DB_HOST environment variable', $warnings);
        $this->assertContains('Missing DB_NAME environment variable', $warnings);
        $this->assertContains('Missing DB_USER environment variable', $warnings);
        $this->assertContains('Static bucket URL not configured; CSS/JS will be local only.', $warnings);
    }

    public function testHealthyWhenDatabaseIsConfigured(): void
    {
        $config = new AppConfig('demo', 'prod', 'db', 'app', 'svc', 'https://cdn.example.com', ['beta']);
        $service = new SystemStatusService($config);

        $summary = $service->summary();
        $warnings = $service->warnings();

        $this->assertSame('healthy', $summary['health']);
        $this->assertSame('configured', $summary['databaseStatus']);
        $this->assertEmpty($summary['missingConfig']);
        $this->assertNotContains('Missing DB_HOST environment variable', $warnings);
    }
}

