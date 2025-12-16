<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;

class HomepageRenderTest extends TestCase
{
    public function testPageRendersConfiguredValues(): void
    {
        $html = $this->renderPage([
            'PROJECT_ID' => 'demo-project',
            'APP_ENV' => 'review',
            'DB_HOST' => 'db.internal',
            'DB_NAME' => 'appdb',
            'DB_USER' => 'svc_user',
            'STATIC_BUCKET_URL' => 'https://cdn.example.com/assets',
            'FEATURE_FLAGS' => 'beta,new-ui',
        ]);

        $this->assertStringContainsString('demo-project', $html);
        $this->assertStringContainsString('review', $html);
        $this->assertStringContainsString('mysql:host=db.internal;dbname=appdb', $html);
        $this->assertStringContainsString('svc_user', $html);
        $this->assertStringContainsString('beta', $html);
        $this->assertStringContainsString('new-ui', $html);
        $this->assertStringContainsString('https://cdn.example.com/assets', $html);
    }

    public function testPageEscapesUserSuppliedData(): void
    {
        $html = $this->renderPage([
            'PROJECT_ID' => '<script>alert("oops")</script>',
            'DB_HOST' => 'db;<script></script>',
            'DB_NAME' => 'app',
        ]);

        $this->assertStringContainsString('&lt;script&gt;alert(&quot;oops&quot;)&lt;/script&gt;', $html);
        $this->assertStringContainsString('db;&lt;script&gt;&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert', $html);
    }

    private function renderPage(array $env): string
    {
        $originalEnv = $_ENV;
        $originalServer = $_SERVER;

        foreach ($env as $key => $value) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }

        ob_start();
        include __DIR__ . '/../../public/index.php';
        $html = (string) ob_get_clean();

        foreach (array_keys($env) as $key) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);
        }

        $_ENV = $originalEnv;
        $_SERVER = $originalServer;

        return $html;
    }
}

