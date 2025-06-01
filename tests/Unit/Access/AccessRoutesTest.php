<?php

namespace Tests\Unit\Access;

use App\Models\User;
use App\Models\Client as ClientModel;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Tests\TestCase;

class AccessRoutesTest extends TestCase
{
    private Client $httpClient;
    private CookieJar $cookieJar;

    public function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://web:8080',
            'allow_redirects' => false
        ]);
        $this->cookieJar = new CookieJar();
    }

    /* 2.1 Teste das rotas protegidas ou privadas */
    public function test_protected_routes_redirect_when_not_authenticated(): void
    {
        $routes = ['/home', '/admin/artworks'];

        foreach ($routes as $route) {
            $response = $this->httpClient->get($route, [
                'cookies' => $this->cookieJar
            ]);
            $this->assertEquals(302, $response->getStatusCode(), "Falha ao testar $route");
            $this->assertEquals('/login', $response->getHeaderLine('Location'));
        }
    }

    /* 2.2 Teste de Rotas publicas */
    public function test_public_routes_are_accessible(): void
    {
        $routes = ['/', '/login'];

        foreach ($routes as $route) {
            $response = $this->httpClient->get($route, [
                'cookies' => $this->cookieJar
            ]);
            $this->assertTrue(
                in_array($response->getStatusCode(), [200, 302]),
                "Falha ao testar $route. Esperado 200 ou 302, recebido: " . $response->getStatusCode()
            );
        }
    }

    /* 2.3 Rotas públicas para usuarios ja autenticados */
    public function test_login_route_redirects_authenticated_user(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123456',
        ]);
        $user->save();

        $client = new ClientModel([
            'phone' => '123456789',
            'user_id' => $user->id
        ]);
        $client->save();

        $this->httpClient->post('/login', [
            'form_params' => [
                'user[email]' => $user->email,
                'user[password]' => '123456',
            ],
            'cookies' => $this->cookieJar
        ]);

        // Tenta acessar '/login' novamente autenticado
        $response = $this->httpClient->get('/login', [
            'cookies' => $this->cookieJar
        ]);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/home', $response->getHeaderLine('Location'));
    }
}
