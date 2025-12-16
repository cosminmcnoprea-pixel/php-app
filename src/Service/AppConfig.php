<?php

namespace App\Service;

/**
 * Represents runtime configuration derived from environment variables.
 */
class AppConfig
{
    private string $projectId;
    private string $environment;
    private string $dbHost;
    private string $dbName;
    private string $dbUser;
    private string $staticBucketUrl;
    /** @var string[] */
    private array $featureFlags;

    public function __construct(
        string $projectId,
        string $environment,
        string $dbHost,
        string $dbName,
        string $dbUser,
        string $staticBucketUrl,
        array $featureFlags = []
    ) {
        $this->projectId = self::sanitizeValue($projectId) ?: 'unknown-project';
        $this->environment = self::sanitizeValue($environment) ?: 'local';
        $this->dbHost = self::sanitizeValue($dbHost);
        $this->dbName = self::sanitizeValue($dbName);
        $this->dbUser = self::sanitizeValue($dbUser);
        $this->staticBucketUrl = self::sanitizeValue($staticBucketUrl);
        $this->featureFlags = self::normalizeFeatureFlags($featureFlags);
    }

    public static function fromGlobals(array $env): self
    {
        return new self(
            self::env($env, 'PROJECT_ID', 'unknown-project'),
            self::env($env, 'APP_ENV', 'local'),
            self::env($env, 'DB_HOST', ''),
            self::env($env, 'DB_NAME', ''),
            self::env($env, 'DB_USER', ''),
            self::env($env, 'STATIC_BUCKET_URL', ''),
            self::normalizeFeatureFlagsFromString(self::env($env, 'FEATURE_FLAGS', ''))
        );
    }

    private static function env(array $env, string $key, string $default): string
    {
        if (array_key_exists($key, $env) && $env[$key] !== '') {
            return (string) $env[$key];
        }

        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        return (string) $value;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getDbHost(): string
    {
        return $this->dbHost;
    }

    public function getDbName(): string
    {
        return $this->dbName;
    }

    public function getDbUser(): string
    {
        return $this->dbUser;
    }

    public function getStaticBucketUrl(): string
    {
        return $this->staticBucketUrl;
    }

    /**
     * @return string[]
     */
    public function getFeatureFlags(): array
    {
        return $this->featureFlags;
    }

    public function isDbConfigured(): bool
    {
        return $this->dbHost !== '' && $this->dbName !== '';
    }

    public function databaseDsn(): ?string
    {
        if (!$this->isDbConfigured()) {
            return null;
        }

        return sprintf('mysql:host=%s;dbname=%s', $this->dbHost, $this->dbName);
    }

    /**
     * @return string[]
     */
    public function missingConfig(): array
    {
        $missing = [];
        if ($this->dbHost === '') {
            $missing[] = 'DB_HOST';
        }
        if ($this->dbName === '') {
            $missing[] = 'DB_NAME';
        }
        if ($this->dbUser === '') {
            $missing[] = 'DB_USER';
        }

        return $missing;
    }

    /**
     * Export values for rendering or diagnostics.
     */
    public function toArray(): array
    {
        return [
            'projectId' => $this->projectId,
            'environment' => $this->environment,
            'dbHost' => $this->dbHost,
            'dbName' => $this->dbName,
            'dbUser' => $this->dbUser,
            'staticBucketUrl' => $this->staticBucketUrl,
            'featureFlags' => $this->featureFlags,
            'databaseDsn' => $this->databaseDsn(),
            'isDbConfigured' => $this->isDbConfigured(),
            'missingConfig' => $this->missingConfig(),
        ];
    }

    private static function sanitizeValue(?string $value): string
    {
        return trim((string) ($value ?? ''));
    }

    /**
     * @param string[] $flags
     * @return string[]
     */
    private static function normalizeFeatureFlags(array $flags): array
    {
        $seen = [];
        $normalized = [];

        foreach ($flags as $flag) {
            $value = self::sanitizeValue($flag);
            if ($value === '' || isset($seen[$value])) {
                continue;
            }
            $seen[$value] = true;
            $normalized[] = $value;
        }

        return $normalized;
    }

    /**
     * @return string[]
     */
    private static function normalizeFeatureFlagsFromString(string $flags): array
    {
        if ($flags === '') {
            return [];
        }

        $parts = preg_split('/[,|]+/', $flags) ?: [];
        return self::normalizeFeatureFlags($parts);
    }
}

