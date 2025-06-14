<?php

namespace Tests\Acceptance;

use App\Models\Artist;
use App\Models\Category;
use App\Models\User;
use App\Models\Artwork;
use Tests\Support\AcceptanceTester;

class ArtworkAcceptanceCest
{
    private User $user;
    private Artist $artist;

    public function _before(AcceptanceTester $I): void
    {
        $category = new Category(['name' => 'Illustration']);
        $category->save();

        $this->user = new User([
            'name' => 'Akira',
            'email' => 'akira@email.com',
            'password' => password_hash('12345', PASSWORD_DEFAULT),
        ]);
        $this->user->save();

        $this->artist = new Artist([
            'bio' => 'See my works',
            'portfolio_url' => 'https://myportfolio.com',
            'user_id' => $this->user->id,
        ]);
        $this->artist->save();

        $I->amOnPage('/artworks');
        $I->see('Portifólio');
        $I->see('Visualize todos os seus projetos');
        $I->see('Minha Arte Visível');
    }

    public function testCreateArtworkWithoutTitle(AcceptanceTester $I): void
    {
        $I->wantTo('Verificar que o sistema não permite cadastrar uma obra sem título');

        $I->amOnPage('/artworks/create');

        $I->fillField('artwork[description]', 'Descrição sem título');
        $I->attachFile('artwork[image]', 'valid-image.jpg');
        $I->selectOption('artwork[category_id]', '1');
        $I->click('Salvar');

        $I->see('O campo título é obrigatório');
        $I->seeInCurrentUrl('/artworks/create');
    }

    public function testCreateArtworkWithInvalidImage(AcceptanceTester $I): void
    {
        $I->wantTo('Evitar cadastro de imagem com tipo inválido');

        $I->amOnPage('/artworks/create');

        $I->fillField('artwork[title]', 'Teste com imagem inválida');
        $I->fillField('artwork[description]', '...');
        $I->attachFile('artwork[image]', 'not-an-image.txt'); // inválido
        $I->selectOption('artwork[category_id]', '1');
        $I->click('Salvar');

        $I->see('O arquivo enviado não é uma imagem válida');
        $I->seeInCurrentUrl('/artworks/create');
    }

    public function testCreateArtworkWithoutCategory(AcceptanceTester $I): void
    {
        $I->wantTo('Garantir que não é possível cadastrar sem categoria');

        $I->amOnPage('/artworks/create');

        $I->fillField('artwork[title]', 'Obra Sem Categoria');
        $I->fillField('artwork[description]', '...');
        $I->attachFile('artwork[image]', 'valid-image.jpg');
        // sem selecionar categoria
        $I->click('Salvar');

        $I->see('Selecione uma categoria válida');
        $I->seeInCurrentUrl('/artworks/create');
    }

    public function testCreateArtworkWithAllFieldsEmpty(AcceptanceTester $I): void
    {
        $I->wantTo('Testar submissão do formulário totalmente vazio');

        $I->amOnPage('/artworks/create');
        $I->click('Salvar');

        $I->see('O campo título é obrigatório');
        $I->see('O campo descrição é obrigatório');
        $I->see('Selecione uma categoria válida');
        $I->see('Uma imagem é obrigatória');
    }

    public function testUpdateArtwork(AcceptanceTester $I): void
    {
        $I->wantTo('Atualizar os dados de uma obra de arte existente');

        $artwork = new Artwork([
            'title' => 'Título Original',
            'creation_date' => date('Y-m-d'),
            'description' => 'Descrição Original',
            'image_url' => '/fake.jpg',
            'artist_id' => $this->artist->id,
            'category_id' => 1
        ]);
        $artwork->save();

        $I->amOnPage("/artworks/{$artwork->id}/edit");
        $I->see('Título Original');

        $I->fillField('artwork[title]', 'Título Atualizado');
        $I->fillField('artwork[description]', 'Descrição Atualizada');
        $I->click('Atualizar');

        $I->seeInCurrentUrl('/admin/page');
        $I->see('Obra atualizada com sucesso!');
    }

    public function testDeleteArtwork(AcceptanceTester $I): void
    {
        $I->wantTo('Remover uma obra de arte do meu portfólio');

        $artwork = new Artwork([
            'title' => 'Obra a ser Deletada',
            'creation_date' => date('Y-m-d'),
            'description' => '...',
            'image_url' => '/fake.jpg',
            'artist_id' => $this->artist->id,
            'category_id' => 1
        ]);
        $artwork->save();

        $I->amOnPage("/artworks/{$artwork->id}/delete");
        $I->seeInCurrentUrl('/admin/page');
        $I->see('Obra Excluida com sucesso');
    }

    public function testDeleteNonExistentArtwork(AcceptanceTester $I): void
    {
        $I->wantTo('Tentar deletar uma obra que não existe');

        $I->amOnPage('/artworks/9999/delete'); // ID fictício
        $I->seeInCurrentUrl('/admin/page');
        $I->see('Obra de Arte não foi encontrada');
    }

    public function testDeleteOtherUsersArtwork(AcceptanceTester $I): void
    {
        $I->wantTo('Impedir que um artista delete obra de outro artista');

        $otherUser = new User([
            'name' => 'Intruso',
            'email' => 'intruso@email.com',
            'password' => password_hash('12345', PASSWORD_DEFAULT),
        ]);
        $otherUser->save();

        $otherArtist = new Artist([
            'bio' => 'Invadindo espaços',
            'portfolio_url' => 'https://pirata.com',
            'user_id' => $otherUser->id,
        ]);
        $otherArtist->save();

        $foreignArtwork = new Artwork([
            'title' => 'Obra de Outro Artista',
            'creation_date' => date('Y-m-d'),
            'description' => 'Não me toque',
            'image_url' => '/hacker.jpg',
            'artist_id' => $otherArtist->id,
            'category_id' => 1
        ]);
        $foreignArtwork->save();

        $I->amOnPage("/artworks/{$foreignArtwork->id}/delete");
        $I->seeInCurrentUrl('/admin/page');
        $I->see('Obra de Arte não foi encontrada'); // mesma resposta que artwork inexistente
    }
}
