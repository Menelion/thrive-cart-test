<?php
declare(strict_types=1);
namespace Tests\Integration\Controller;

use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\App;

class BasketControllerIntegrationTest extends TestCase
{
    private App $app;
    
    // Store original session configuration to restore later
    private array $originalSessionConfig = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Store original session configuration settings
        $this->originalSessionConfig = [
            'use_strict_mode' => ini_get('session.use_strict_mode'),
            'cookie_secure' => ini_get('session.cookie_secure'),
            'cookie_httponly' => ini_get('session.cookie_httponly')
        ];

        $this->app = createApp();
    }

    protected function tearDown(): void
    {
        // Clean up any session data that might have been created during the test
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
            $_SESSION = [];
        }
        
        // Restore original session configuration
        foreach ($this->originalSessionConfig as $key => $value) {
            ini_set("session.$key", $value);
        }
        
        parent::tearDown();
    }

    public function testAddProductToBasket(): void
    {
        $request = (new ServerRequestFactory)->createServerRequest('POST', '/basket/add/R01');
        $response = $this->app->handle($request);

        $this->assertEquals(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());
        $responseBody = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('products', $responseBody);
    }
}
