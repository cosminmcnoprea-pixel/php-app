## app (PHP)

Simple PHP-FPM + nginx container meant for Cloud Run.

### Short summary
- `public/index.php` – homepage with a couple of runtime details (env/config)
- `src/Service/*` – small “config/status” helpers (easy to unit test)
- `tests/` – PHPUnit unit + basic integration rendering tests
- `.github/workflows/deploy*.yml` – dev deploy + prod promote (uses shared actions repo)

### Run tests locally

```bash
composer install
composer test
```

### Deploy
This repo expects Workload Identity Federation (no JSON keys).

Secrets used by the workflows:
- `WIF_PROVIDER_DEV`, `WIF_SERVICE_ACCOUNT_DEV`
- `WIF_PROVIDER_PROD`, `WIF_SERVICE_ACCOUNT_PROD`

Dev deploy is on pushes to `main`. Prod is a manual workflow where you promote a dev tag to a prod tag.

### Access the app
Once infra + deploy are done, you normally hit the HTTPS Load Balancer IP / domain.
The simplest way is to grab the LB IP using the script from `https://github.com/cosminmcnoprea-pixel/bash-scripting/actions`.
