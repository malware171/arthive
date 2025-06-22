<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

use App\Models\Artist;
use App\Models\User;
use Codeception\Module;

class LoginHelper extends Module
{
    public function loginAsArtist(): void
    {
        $userArtist = new User([
            'name' => 'Artist',
            'email' => 'artist@test.com',
            'password' => password_hash('12345', PASSWORD_DEFAULT)
        ]);
        $userArtist->save();

        $artist = new Artist([
            'bio' => 'I am artist',
            'portfolio_url' => 'https://teste',
            'ai_detection_count' => 0,
            'user_id' => $userArtist->id
        ]);
        $artist->save();

        /** @var \Tests\Support\AcceptanceTester $page */
        $page = $this->getModule('WebDriver');
        $page->amOnPage('/login');
        $page->fillField('user[email]', 'artist@test.com');
        $page->fillField('user[password]', '12345');
        $page->click('Login');
    }

    public function logout(): void
    {
        /** @var \Tests\Support\AcceptanceTester $page */
        $page = $this->getModule('WebDriver');
        $page->click('fulano@example.com');
        $page->click('Sair');
    }
}
