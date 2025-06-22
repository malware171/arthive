<?php

namespace Tests\Acceptance\ArtworkCrud;


use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;
use App\Models\User;
use App\Models\Artist;
use App\Models\Category;

class ArtworkUploadCest extends BaseAcceptanceCest
{
    public function tryUploadWithInvalidData(AcceptanceTester $I): void
    {
        $this->loginArtist($I);

        $I->amOnPage('/admin/artworks/new');
        $I->see('Crie seu post');
    }

    public function tryUploadWithValidData(AcceptanceTester $I): void
    {
        $this->loginArtist($I);

        $category = new \App\Models\Category([
            'name' => 'Categoria Teste'
        ]);
        $category->save();;

        $I->amOnPage('/admin/artworks/new');
        $I->see('Crie seu post');

        $I->fillField('artwork[title]', 'Minha Arte Teste');
        $I->fillField('artwork[description]', 'Descrição da arte teste');

        $I->selectOption('artwork[category_id]', $category->id);

        $I->attachFile('image', 'test-image.jpg');

        $I->click('Publicar post');

        $I->see('Sucesso');
        $I->seeInCurrentUrl('/admin/artworks');
    }

    public function loginArtist(AcceptanceTester $page): void
    {
        $userArtist = new User([
            'name' => 'User_Artist',
            'email' => 'artist@example.com',
            'password' => '123456',
        ]);
        $userArtist->save();

        $artist = new Artist([
            'bio' => 'me siga',
            'portfolio_url' => 'htps://teste',
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
}
