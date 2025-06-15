<?php

namespace Tests\Acceptance\ArtworkCrud;

use App\Controllers\ArtistController;
use App\Controllers\ArtworkController;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Core\Constants\Constants;
use App\Models\User;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Category;
use Tests\Unit\Controllers\ControllerTestCase;

class ArtworkTestController extends ControllerTestCase
{
    private string $imagePath;
    private Artist $user;

    public function setUp(): void
    {
        parent::setUp();

        //criando categoria para teste
        $category = new Category(['name' => 'Illustration']);
        $category->save();

        //caminho da imagem salva
        $this->imagePath = Constants::rootPath()->join('tests/resources/images/test.png');

        //criando um artista vinculado ao user
        $this->user = new Artist([
            'bio' => "Hello, I'm an artist",
            'portfolio_url' => 'https://myportifolio.com',
            'ai_detection_count' => 0,
            'user_id' => $this->createFakeUser()->id,
        ]);
        $this->user->save();

        //simulanndo login
        Auth::login($this->user);
    }

    public function tearDown(): void
    {
        Auth::logout();
        parent::tearDown();
    }

    //tentativa de cadastro com dados invalidos
    public function testRegistrationWithInvalidData(): void
    {
        $artwork = $this->createFakeArtwork();

        $params = [
            'artwork' => [
                'title' => '',
                'description' => '',
                'category_id' => '',
            ],
        ];

        $_FILES['image'] = [
            'name' => 'Testfake.png',
            'type' => 'image.png',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0,
        ];

        $response = $this->post('artwork', ArtworkController::class, $params);
        $flash = $_SESSION['flash']['danger'] ?? null;
        $this->assertEquals('Todos os campos devem ser preechidos', $flash);
    }

    //teste com dados válidos
    public function testRegistrationWithValidData(): void
    {
        $artwork = $this->createFakeArtwork();
        $params = [
            'artwork' => [
                'title' => 'Artwork Test',
                'description' => 'Artwork description',
                'category_id' => 1,
            ],
        ];

        $_FILES['image'] = [
            'name' => 'Test.png',
            'type' => 'image.png',
            'tmp_name' => $this->imagePath,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->imagePath)
        ];

        $this->post('artwork', ArtworkController::class, $params);
        $flash = $_SESSION['flash']['success'] ?? null;
        $this->assertEquals('Imagem salva com sucesso', $flash);

        $savedArtwork = Artwork::findById($artwork->id);
        $this->assertNotNull($savedArtwork, 'Artwork não foi criada no banco de dados');
        $this->assertEquals('Artwork description', $savedArtwork->description);
        $this->assertEquals(1, $savedArtwork->category_id);
    }

    //teste de edição com dados incorretos
    public function testUpdatingWithInvalidData(): void
    {
        $artwork = $this->createFakeArtwork();

        $params = [
            'id' => $artwork->id,
            'artwork' => [
                'title' => '',
                'description' => '',
                'category_id' => '',
            ],
        ];


        $this->post('update', ArtworkController::class, $params);
        $flash = $_SESSION['flash']['danger'] ?? null;
        $this->assertEquals('Titulo, descrição e categoria são obrigatórios.', $flash);
    }

    //teste de edição com dados corretos
    public function testUpdatingWithValidData(): void
    {
        $artwork = $this->createFakeArtwork();

        $params = [
            'id' => $artwork->id,
            'artwork' => [
                'title' => 'New title',
                'description' => 'New description',
                'category_id' => 1,
            ],
        ];

        $this->post('update', ArtworkController::class, $params);
        $flash = $_SESSION['flash']['success'] ?? null;
        $this->assertEquals('Obra atualizada com sucesso!', $flash);

        $updatedArtwork = Artwork::findById($artwork->id);
        $this->assertEquals('New title', $updatedArtwork->title);
        $this->assertEquals('New description', $updatedArtwork->description);
        $this->assertEquals(1, $updatedArtwork->category_id);
    }

    //listando artworks
    public function testListArtworks(): void
    {
        $this->createFakeArtwork();
        $this->createFakeArtwork();

        $output = $this->get('index', ArtworkController::class);
        $this->assertStringContainsString('Visualize todos os seus projetos', $output);
    }

    //deletando obras de arte
    public function testDeletingArtworks(): void
    {
        $artwork = $this->createFakeArtwork();

        $params = ['id' => $artwork->id];

        $this->post('destroy', ArtworkController::class, $params);
        $flash = $_SESSION['flash']['success'] ?? null;
        $this->assertEquals('Obra excluida com sucesso', $flash);
        $deletedArtwork = Artwork::findById($artwork->id);
        $this->assertNull($deletedArtwork, 'Artwork ainda existe no banco após tentativa de exclusão.');
    }

    private function createFakeUser(): User
    {
        $user = new User([
            'name' => 'Robert',
            'email' => 'robert@email.com',
            'password' => password_hash('12345', PASSWORD_DEFAULT),
        ]);
        $user->save();
        return $user;
    }

    private function createFakeArtwork(): \App\Models\Artwork
    {
        $artwork = new \App\Models\Artwork([
            'title' => 'Teste',
            'description' => 'Descrição',
            'creation_date' => date('Y-m-d'),
            'image_url' => '/uploads/fake.jpg',
            'is_ai_verified' => 0,
            'artist_id' => Auth::user()->id,
            'category_id' => 1,
        ]);
        $artwork->save();
        return $artwork;
    }

    public function testDeletingArtworkOfAnotherUser(): void
    {
        $otherUser = new Artist([
            'bio' => "I'm another artist",
            'portfolio_url' => 'https://otherportfolio.com',
            'ai_detection_count' => 0,
            'user_id' => $this->createFakeUser()->id,
        ]);
        $otherUser->save();

        $artworkOfOtherUser = new Artwork([
            'title' => 'Other User Artwork',
            'description' => 'Artwork belonging to another user',
            'creation_date' => date('Y-m-d'),
            'image_url' => '/uploads/fake_other.jpg',
            'is_ai_verified' => 0,
            'artist_id' => $otherUser->id,
            'category_id' => 1,
        ]);
        $artworkOfOtherUser->save();

        $params = ['id' => $artworkOfOtherUser->id];
        $response = $this->post('destroy', ArtworkController::class, $params);


        $flash = $_SESSION['flash']['danger'] ?? null;
        $this->assertEquals('Você não tem permissão para excluir essa obra.', $flash);

        $stillExists = Artwork::findById($artworkOfOtherUser->id);
        $this->assertNotNull($stillExists, 'A obra de outro usuário foi excluída indevidamente.');
    }
}
