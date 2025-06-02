<?php

namespace App\Controllers;

use App\Models\Artwork;
use App\Models\Category;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class ArtworkController extends Controller
{
    public function index(Request $request): void
    {
        $artist = Auth::user();

        $artworks = Artwork::where(['artist_id' => $artist->id]);

        $title = 'Portifólio';
        $subtitle = 'Visualize todos os seus projetos';
        $this->render('admin/artworks/index', compact('title', 'subtitle', 'artworks', 'artist'));
    }
    public function new(Request $request): void
    {
        $params = $request->getParams();
        $categories = Category::all();
        $artwork = new Artwork([]);

        $title = 'Nova Arte';
        $description = 'Descrição';
        $this->render('/admin/artworks/new', compact('title', 'description', 'categories', 'artwork'));
    }

    public function create(Request $request): void
    {
        $params = $request->getParam('artwork');
        $imageFile = $_FILES['image'] ?? null;

        $artwork = new Artwork([
            'title' => $params['title'],
            'creation_date' => date('Y-m-d'),
            'description' => $params['description'],
            'is_ai_verified' => 0,
            'artist_id' => Auth::user()->id,
            'category_id' => $params['category_id']
        ]);

        if (!$artwork->save()) {
            FlashMessage::danger('Existem dados incorretos');
            $this->redirectTo(route('artwork.new'));
            return;
        }

        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
            $artwork->image()->update($imageFile);
        }

        FlashMessage::success('Obra criada com sucesso!');
        $this->redirectTo(route('artist.admin.page'));
    }

    public function destroy(Request $request): void
    {
        $params = $request->getParams();

        $artwork = Artwork::findById((int)$params['id']);

        if ($artwork) {
            $artwork->image()->removeOldImage();

            if ($artwork->destroy()) {
                FlashMessage::success('Obra Excluida com sucesso');
            } else {
                FlashMessage::danger('Erro ao excluir a obra');
            }
        } else {
            FlashMessage::danger('Obra de Arte não foi encontrada');
        }

        $this->redirectTo(route('artist.admin.page'));
    }

    public function edit(Request $request): void
    {
        $params = $request->getParams();
        $categories = Category::all();

        $artwork = Artwork::findById($params['id']);

        if ($artwork) {
            $this->render('/admin/artworks/edit', compact('artwork', 'categories'));
        } else {
            FlashMessage::danger('Arte não foi encontrada');
        }
    }

    // Em App\Controllers\ArtworkController.php

    public function update(Request $request): void
    {
        $routeParams = $request->getParams();
        $artworkId = (int) $routeParams['id'];
        $params = $request->getParam('artwork');

        $imageFile = $_FILES['image'] ?? null;

        $artwork = Artwork::findById($artworkId);

        $artwork->title = $params['title'];
        $artwork->description = $params['description'];
        $artwork->category_id = $params['category_id'];

        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
            $artwork->image()->update($imageFile);
        }

        if (!$artwork->save()) {
            FlashMessage::danger('Existem dados incorretos');
            $this->redirectTo(route('artwork.edit', ['id' => $artworkId]));
        } else {
            FlashMessage::success('Obra atualizada com sucesso!');
            $this->redirectTo(route('artist.admin.page'));
        }
    }
}
