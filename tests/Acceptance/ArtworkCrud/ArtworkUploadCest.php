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

    public function tryUpdateImageWithValidData(AcceptanceTester $I): void
    {
        $artistId = $this->loginArtist($I);

        // Criar categoria
        $category = new \App\Models\Category([
            'name' => 'Categoria Atualizar'
        ]);
        $category->save();

        $uploadDir = codecept_root_dir("public/assets/uploads/artworks/{$artistId}/");
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        copy(codecept_data_dir('test-image.jpg'), $uploadDir . 'test-image.jpg');

        // Criar obra com imagem inicial
        $initialArtwork = new \App\Models\Artwork([
            'title' => 'Arte Antiga',
            'description' => 'Descrição antiga',
            'creation_date' => date('Y-m-d'),
            'image_url' => 'artwork.jpg',
            'is_ai_verified' => 0,
            'artist_id' => $artistId,
            'category_id' => $category->id
        ]);
        $initialArtwork->save();

        // Acessa página de edição
        $I->amOnPage('/admin/artworks/' . $initialArtwork->id . '/edit');
        $I->see('Edite seu post');


        // Atualiza campos
        $I->fillField('artwork[title]', 'Arte Atualizada');
        $I->fillField('artwork[description]', 'Descrição nova');
        $I->selectOption('artwork[category_id]', $category->id);
        $I->attachFile('image', 'test-image-updated.jpg');

        $I->click('Editar post');

        $I->see('Obra atualizada com sucesso!');
        $I->seeInCurrentUrl('/admin/artworks');
    }

    public function tryDeleteArtworkWithImage(AcceptanceTester $I): void
    {
        $artistId = $this->loginArtist($I);

        // Criar categoria
        $category = new \App\Models\Category([
            'name' => 'Categoria Deletar'
        ]);
        $category->save();

        // Criar artwork com imagem associada
        $artwork = new \App\Models\Artwork([
            'title' => 'Arte pra deletar',
            'description' => 'Imagem que será removida',
            'creation_date' => date('Y-m-d'),
            'image_url' => 'artwork.jpg',
            'is_ai_verified' => 0,
            'artist_id' => $artistId,
            'category_id' => $category->id
        ]);
        $artwork->save();

        // Copiar a imagem para a pasta simulando upload real
        $uploadDir = codecept_root_dir("public/assets/uploads/artworks/{$artwork->id}/");
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        copy(codecept_data_dir('test-image.jpg'), $uploadDir . 'artwork.jpg');

        $I->amOnPage('/admin/artworks');
        $I->see($artwork->title);

        $I->click("button[data-modal-target='popup-modal-{$artwork->id}']");
        $I->waitForElementVisible("#popup-modal-{$artwork->id}");

        $I->click("#popup-modal-{$artwork->id} button[type='submit']");

        $I->dontSee($artwork->title);
    }

    public function loginArtist(AcceptanceTester $page): int
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

        return $artist->id;
    }
}
