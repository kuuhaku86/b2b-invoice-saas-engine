<?php

namespace Tests;

use App\Models\Tenant;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    /** @var array<int, string> */
    private array $createdTenantIds = [];

    /**
     * Provisions a real tenant database against whatever the `app`
     * container's own environment points at — i.e. the real local dev
     * `saas_central` database, not an isolated testing one. Browser tests
     * exercise the actual running stack a human would hit, so there's no
     * separate DB to swap in the way Feature tests do (see Tests\TestCase).
     * Stick to the reserved e2e* tenant ids aliased in docker-compose.yml.
     */
    protected function createTestTenant(string $id, array $attributes = []): Tenant
    {
        $tenant = Tenant::create(array_merge(['id' => $id], $attributes));
        $tenant->domains()->create(['domain' => $id]);
        $this->createdTenantIds[] = $id;

        return $tenant;
    }

    protected function tenantUrl(string $tenantId, string $path = '/'): string
    {
        return 'http://' . $tenantId . '.' . env('CENTRAL_DOMAIN', 'saas.test') . $path;
    }

    protected function tearDown(): void
    {
        foreach ($this->createdTenantIds as $id) {
            Tenant::find($id)?->delete();
        }

        $this->createdTenantIds = [];

        parent::tearDown();
    }

    /**
     * Prepare for Dusk test execution.
     *
     * Never starts a local ChromeDriver: the app container has no Chrome
     * binary to drive (it's PHP-only — see docker/Dockerfile). Tests always
     * connect to the `selenium` service's remote WebDriver grid instead
     * (see docker-compose.yml and driver() below).
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        //
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://selenium:4444/wd/hub',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
