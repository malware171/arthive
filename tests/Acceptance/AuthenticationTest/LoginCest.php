<?php

namespace Tests\Acceptance\AuthenticationTest;

use App\Models\Artist;
use App\Models\Client;
use App\Models\User;
use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class LoginCest extends BaseAcceptanceCest
{
    public function testTryAccessWithWrongFields(AcceptanceTester $page): void
    {
        $page->amOnPage('/login');

        $page->fillField('user[email]', 'usuarioInvalido@gmail.com');
        $page->fillField('user[password]', '123456');

        $page->click('Login');

        $page->see('Invalid username or password');
        $page->seeInCurrentUrl('/login');
    }

    public function testTryAccessPageNotAuthenticated(AcceptanceTester $page): void
    {
        $page->amOnPage('/login');
        $page->click('Login');

        $page->see('Email and password are required');
    }

    public function testLoginSuccessAdminOrArtist(AcceptanceTester $page): void
    {
        $userArtist = new User([
            'name' => 'User_Artist',
            'email' => 'artist@example.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);
        $userArtist->save();

        $artist = new Artist([
            'bio' => 'me siga',
            'portfolio_url' => 'hhtps://teste',
            'ai_detection_count' => 0,
            'user_id' => $userArtist->id
        ]);
        $artist->save();

        $page->amOnPage('/login');

        $page->fillField('user[email]', $userArtist->email);
        $page->fillField('user[password]', $userArtist->password);

        $page->click('Login');

        $page->see('Login successful');
        $page->seeInCurrentUrl('/admin/artworks');
    }

    public function testLoginSuccessClientUser(AcceptanceTester $page): void
    {
        $clientUser = new User([
            'name' => 'User_Client',
            'email' => 'clientTest@example.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);
        $clientUser->save();

        $client = new Client([
            'phone' => '42998247591',
            'user_id' => $clientUser->id
        ]);
        $client->save();

        $page->amOnPage('/login');

        $page->fillField('user[email]', $clientUser->email);
        $page->fillField('user[password]', $clientUser->password);

        $page->click('Login');

        $page->see('Login successful');
        $page->seeInCurrentUrl('/home');
    }

    public function testLogout(AcceptanceTester $page): void
    {
        $clientUser = new User([
            'name' => 'User_Client',
            'email' => 'clientTest@example.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);
        $clientUser->save();

        $client = new Client([
            'phone' => '42998247591',
            'user_id' => $clientUser->id
        ]);
        $client->save();

        $page->amOnPage('/login');

        $page->fillField('user[email]', $clientUser->email);
        $page->fillField('user[password]', $clientUser->password);

        $page->click('Login');

        $page->seeInCurrentUrl('/home');

        $page->click('Logout');
        $page->see('Logout successful');
        $page->seeInCurrentUrl('/login');
    }
}
