<?php

namespace Tests\Unit\Models\Users;

use Tests\TestCase;
use App\Models\Artist;
use App\Models\Client;
use App\Models\User;

use function PHPUnit\Framework\assertFalse;

class ArtistTest extends TestCase
{
    private User $user_artist;
    private Artist $artist;

    public function setUp(): void
    {

        parent::setUp();

        $this->user_artist = new User([
         'name' => 'User_Artist',
         'email' => 'artist@example.com',
         'password' => '123456',
         'password_confirmation' => '123456'
        ]);
        $this->user_artist->save();

        $this->artist = new Artist([
        'bio' => 'me siga',
        'portfolio_url' => 'hhtps://teste',
        'ai_detection_count' => 0,
        'user_id' => $this->user_artist->id
        ]);
        $this->artist->save();
    }

    public function test_should_create_new_client(): void
    {
        $newUser = new User([
         'name' => 'New User Artist',
         'email' => 'newartist@example.com',
         'password' => '123456',
         'password_confirmation' => '123456'
        ]);
        $newUser->save();

        $newArtist = new Artist([
        'bio' => 'me siga',
        'portfolio_url' => 'hhtps://teste',
        'ai_detection_count' => 0,
        'user_id' => $newUser->id
        ]);
        $newArtist->save();

        $this->assertCount(2, Artist::all());
    }

    public function test_valid_artist_is_user(): void
    {
        $artist = new Artist([
        'bio' => 'me siga',
        'portfolio_url' => 'hhtps://teste',
        'ai_detection_count' => 0,
        ]);
        $artist->save();

        $this->assertFalse($artist->isValid());
    }

    public function test_should_return_an_error(): void
    {
        $artist = new Artist([
        'bio' => 'me siga',
        'portfolio_url' => 'hhtps://teste',
        'ai_detection_count' => 0,
        ]);

        $artist->validates();

        $errors = $artist->getErrors();

        $this->assertCount(1, $errors);
    }
}
