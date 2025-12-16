<?php

namespace App\Service;

/**
 * Produces a human-friendly snapshot of the application's runtime status.
 */
class SystemStatusService
{
    public function __construct(private readonly AppConfig $config)
    {
    }

    /**
     * @return array<string,mixed>
     */
    public function summary(): array
    {
        $missing = $this->config->missingConfig();

        return [
            'projectId' => $this->config->getProjectId(),
            'environment' => $this->config->getEnvironment(),
            'featureFlags' => $this->config->getFeatureFlags(),
            'dbHost' => $this->config->getDbHost(),
            'dbName' => $this->config->getDbName(),
            'dbUser' => $this->config->getDbUser(),
            'databaseDsn' => $this->config->databaseDsn(),
            'databaseStatus' => $this->config->isDbConfigured() ? 'configured' : 'missing',
            'missingConfig' => $missing,
            'health' => $this->healthState($missing),
            'phpVersion' => PHP_VERSION,
            'timestamp' => gmdate('c'),
            'staticAssetsConfigured' => $this->config->getStaticBucketUrl() !== '',
            'staticBucketUrl' => $this->config->getStaticBucketUrl(),
        ];
    }

    /**
     * @return string[]
     */
    public function warnings(): array
    {
        $warnings = [];

        foreach ($this->config->missingConfig() as $missing) {
            $warnings[] = sprintf('Missing %s environment variable', $missing);
        }

        if ($this->config->getStaticBucketUrl() === '') {
            $warnings[] = 'Static bucket URL not configured; CSS/JS will be local only.';
        }

        return $warnings;
    }

    /**
     * @param string[] $missing
     */
    private function healthState(array $missing): string
    {
        return empty($missing) ? 'healthy' : 'degraded';
    }
}

