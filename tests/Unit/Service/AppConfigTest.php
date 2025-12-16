<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\AppConfig;
use PHPUnit\Framework\TestCase;

class AppConfigTest extends TestCase
{
    public function testDefaultsAreAppliedWhenEnvMissing(): void
    {
        $config = new AppConfig('', '', '', '', '', '', []);

        $this->assertSame('unknown-project', $config->getProjectId());
        $this->assertSame('local', $config->getEnvironment());
        $this->assertFalse($config->isDbConfigured());
        $this->assertNull($config->databaseDsn());
        $this->assertSame(['DB_HOST', 'DB_NAME', 'DB_USER'], $config->missingConfig());
    }

    public function testBuildsFromGlobals(): void
    {
        $config = AppConfig::fromGlobals([
            'PROJECT_ID' => 'demo',
            'APP_ENV' => 'staging',
            'DB_HOST' => 'mysql.internal',
            'DB_NAME' => 'appdb',
            'DB_USER' => 'svc_user',
            'STATIC_BUCKET_URL' => 'https://cdn.example.com',
            'FEATURE_FLAGS' => 'beta,new-ui,beta', // duplicate should be removed
        ]);

        $this->assertSame('demo', $config->getProjectId());
        $this->assertSame('staging', $config->getEnvironment());
        $this->assertTrue($config->isDbConfigured());
        $this->assertSame('mysql:host=mysql.internal;dbname=appdb', $config->databaseDsn());
        $this->assertSame(['beta', 'new-ui'], $config->getFeatureFlags());
        $this->assertSame('https://cdn.example.com', $config->getStaticBucketUrl());
    }

    public function testFeatureFlagsAreTrimmedAndUnique(): void
    {
        $config = new AppConfig('proj', 'dev', '', '', '', '', ['  alpha ', 'beta', 'ALPHA', 'beta']);

        $this->assertSame(['alpha', 'beta', 'ALPHA'], $config->getFeatureFlags());
    }
}

