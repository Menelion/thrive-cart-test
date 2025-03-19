<?php
declare(strict_types=1);
namespace Tests\Integration\Controller;

use Fig\Http\Message\StatusCodeInterface;
use ParagonIE\EasyDB\EasyDB;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Stream as SlimStream;
use Slim\App;

class UserControllerIntegrationTest extends TestCase
{
    private App $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = createApp();
    }

    protected function tearDown(): void
    {
        $container = $this->app->getContainer();
        $db = $container->get(EasyDB::class);
        $db->run('DELETE FROM users');
        $db->run('ALTER TABLE users AUTO_INCREMENT = 1');
        $db->run('DELETE FROM charges');
        $db->run('ALTER TABLE charges AUTO_INCREMENT = 1');
    }

    public function testAddDeleteUserSuccess(): void
    {
        $payload = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
        ];

        $request = (new ServerRequestFactory)->createServerRequest('POST', '/user')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->createStream(json_encode($payload)));

        $response = $this->app->handle($request);

        $this->assertEquals(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());
        $responseBody = json_decode((string)$response->getBody(), true);
        $this->assertEquals('john.doe@example.com', $responseBody['email']);
        $this->assertNotEmpty($responseBody['id']);

        $request = (new ServerRequestFactory)->createServerRequest('DELETE', '/user/' . $responseBody['id']);
        $response = $this->app->handle($request);

        $this->assertEquals(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }

    public function testCreateUserValidationFailure(): void
    {
        $payload = [
            'firstName' => 'John',
            'lastName' => 'Doe',
        ];

        $request = (new ServerRequestFactory)->createServerRequest('POST', '/user')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->createStream(json_encode($payload)));

        $response = $this->app->handle($request);

        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $responseBody = json_decode((string)$response->getBody(), true);
    }

    public function testCharges(): void
    {
        $payload = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
        ];

        $request = (new ServerRequestFactory)->createServerRequest('POST', '/user')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->createStream(json_encode($payload)));

        $response = $this->app->handle($request);
        $responseBody = json_decode((string)$response->getBody(), true);
        $userId = $responseBody['id'];

        $this->assertEquals(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());

        $charge1 = [
            'energy' => 7500,
            'cost' => 3000,
            'isSuccessful' => true,
        ];
        $charge2 = [
            'energy' => 10000,
            'cost' => 4000,
            'isSuccessful' => false,
        ];
        $charge3 = [
            'energy' => 5000,
            'cost' => 2000,
            'isSuccessful' => true,
        ];

        $request = (new ServerRequestFactory)->createServerRequest('POST', '/user/' . $userId . '/charge')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->createStream(json_encode($charge1)));
        $response = $this->app->handle($request);
        $this->assertEquals(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());

        $request = (new ServerRequestFactory)->createServerRequest('POST', '/user/' . $userId . '/charge')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->createStream(json_encode($charge2)));
        $response = $this->app->handle($request);
        $this->assertEquals(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());

        $request = (new ServerRequestFactory)->createServerRequest('POST', '/user/' . $userId . '/charge')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->createStream(json_encode($charge3)));
        $response = $this->app->handle($request);
        $this->assertEquals(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());

        $request = (new ServerRequestFactory)->createServerRequest('GET', '/user/' . $userId . '/statistics');
        $response = $this->app->handle($request);
        $this->assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $responseBody = json_decode((string)$response->getBody(), true);

        $this->assertEquals(3, $responseBody['chargesNumber']);
        $this->assertEquals(12.5, $responseBody['totalEnergy']);
        $this->assertEquals(25.0, $responseBody['averageCost']);
    }
    private function createStream(string $content): \Slim\Psr7\Stream
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $content);
        rewind($stream);
        return new SlimStream($stream);
    }
}
