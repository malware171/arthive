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

        $artworks = Artwork::where(['artist_id' => $artist->id ]);

        $title = 'Portifólio';
        $subtitle = 'Visualize todos os seus projetos';
        $this->render('admin/artworks/index', compact('title', 'subtitle', 'artworks', 'artist'));
    }
    public function new(Request $request): void
    {
        $params = $request->getParams();
        $categories = Category::all();

        $title = 'Nova Arte';
        $description = 'Descrição';
        $this->render('/admin/artworks/new', compact('title', 'description', 'categories'));
    }

    public function newArtwork(Request $request): void
    {
        $params = $request->getParam('artwork');
        $imgFile = $_FILES['image'];

        if (
            !is_array($params) ||
            empty($params['title']) ||
            empty($params['description']) ||
            empty($params['category_id']) ||
            !isset($imgFile) ||
            $imgFile['error'] !== UPLOAD_ERR_OK
        ) {
            FlashMessage::danger('Todos os campos devem ser preechidos');
            $this->redirectTo(route('artist.new'));
            return;
        }

        $artist = Auth::user();

        $uploadDir =  __DIR__ . "/../../public/assets/uploads/artworks/{$artist->id}/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $imageName = uniqid() . '-' . basename($imgFile['name']);
        $targetFile = $uploadDir . $imageName;

        if (!move_uploaded_file($imgFile['tmp_name'], $targetFile)) {
            FlashMessage::danger('Ocorreu um erro ao salvar a imagem.');
            $this->redirectTo(route('artist.new'));
            return;
        }

        $imageUrl = "/uploads/artworks/{$artist->id}/" . $imageName;

        $artwork = new Artwork([
         'title' => $params['title'],
         'creation_date' => date('Y-m-d'),
         'description' => $params['description'],
         'image_url' => $imageUrl,
         'is_ai_verified' => 0,
         'artist_id' => $artist->id,
         'category_id' => $params['category_id']
        ]);

        if (!$artwork->save()) {
            FlashMessage::danger('Erro ao salvara imagem');
            $this->redirectTo(route('artist.new'));
        } else {
            FlashMessage::success('Imagem salva com sucesso');
            $this->redirectTo(route('artist.admin.page'));
        }
    }
    public function destroy(Request $request): void
    {
        $params = $request->getParams();

        $artwork = $this->current_user->artist()->artworks()->findById($params['id']);

        if ($artwork) {
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
        
        $artwork = $this->current_user->artist()->artworks()->findById($params['id']);

        if($artwork) {
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
        $imgFile = $_FILES['image'] ?? null;

        $artwork = $this->current_user->artist()->artworks()->findById($artworkId);

        if (!$artwork) {
            FlashMessage::danger('Arte não encontrada para atualização.');
            $this->redirectTo(route('artist.admin.page')); 
            return;
        }
        
        if (empty($params['title']) || empty($params['description']) || empty($params['category_id'])) {
            FlashMessage::danger('Título, descrição e categoria são obrigatórios.');
            $this->redirectTo(route('artwork.edit', ['id' => $artworkId]));
            return;
        }
        
        $artist = Auth::user(); 
        $imageUrl = $artwork->image_url; 

        if ($imgFile && $imgFile['error'] === UPLOAD_ERR_OK) {
            
            $uploadDir =  __DIR__ . "/../../public/assets/uploads/artworks/{$artist->id}/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $imageName = uniqid() . '-' . basename($imgFile['name']);
            $targetFile = $uploadDir . $imageName;

            if (!move_uploaded_file($imgFile['tmp_name'], $targetFile)) {
                FlashMessage::danger('Ocorreu um erro ao salvar a nova imagem.');
                $this->redirectTo(route('artwork.edit', ['id' => $artworkId]));
                return;
            }
            $imageUrl = "/uploads/artworks/{$artist->id}/" . $imageName;
        }
        
        $artwork->title = $params['title'];
        $artwork->description = $params['description'];
        $artwork->image_url = $imageUrl; 
        $artwork->category_id = $params['category_id'];

        if (!$artwork->save()) {
            FlashMessage::danger('Erro ao salvar as alterações.');
            $this->redirectTo(route('artwork.edit', ['id' => $artworkId]));
        } else {
            FlashMessage::success('Obra atualizada com sucesso!');
            $this->redirectTo(route('artist.admin.page'));
        }
    }
}
