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
    public function new(Request $request): void
    {
        $params = $request->getParams();
        $categories = Category::all();

        $title = 'Nova Arte';
        $description = 'Descrição';
        $this->render('adminPage/createPost/new', compact('title', 'description', 'categories'));
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

        $imageUrl = '/uploads/artworks/' . $imageName;

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
            $this->redirectTo(route('artist.index'));
        }
    }
}
