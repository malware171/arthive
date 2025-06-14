<?php

namespace App\Controllers;

use App\Models\Artwork;
use Core\Http\Request;
use Core\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(Request $request): void
    {
        $page = (int) $request->getParam('page', 1);

        $paginator = Artwork::paginate(
            page: $page,
            per_page: 10,
        );

        $title = 'Todas as obras';

        $friend = $this->randomFolower();
        $folowers = $this->randomFolowers();

        $this->render('home/index', compact('title', 'paginator', 'friend', 'folowers'));
    }

    private function randomFolower(): string
    {
        $namesList = [
        "Carlos", "Ana", "Pedro", "Mariana", "Lucas", "Juliana", "Rafael", 
        "Fernanda", "Gabriel", "Beatriz", "Thiago", "Larissa", "Mateus", "Camila"
        ];

        $randomIndex = array_rand($namesList);

        $name = $namesList[$randomIndex];

        return "https://api.dicebear.com/8.x/adventurer/svg?seed={$name}";
    }

    private function randomFolowers(): int
    {
        $randomNumber = rand(0, 99);
        return $randomNumber;
    }
}
