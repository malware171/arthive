<?php

namespace Database\Populate;

use App\Models\User;
use App\Models\Artist;
use App\Models\Client;
use Core\Database\Database;

class UserPopulate
{
  public static function populate(): void
  {

    $user1 = new User([
      'name' => 'Alice Artist',
      'email' => 'aliceArtist@email.com',
      'password' => 'password123',
      'type' => 'artist',
      'avatar_url' => null
    ]);
    $user1->save();

    $artist1 = new Artist([
      'bio' => "Alice's bio",
      'portfolio_url' => '',
      'ai_detection_count' => 0,
      'user_id' => $user1->id
    ]);
    $artist1->save();

    $numberOfUsers = 2;

    for ($i = 0; $i < $numberOfUsers; $i++) {
      $artistUser = new User([
        'name' => 'Artist ' . $i . 'Name',
        'email' => 'artist' . $i . '@email.com',
        'password' => 'password123',
        'type' => 'artist',
        'avatar_url' => null
      ]);
      $artistUser->save();

      $artist = new Artist([
        'bio' => "Artist's bio " . $i,
        'portfolio_url' => '',
        'ai_detection_count' => 0,
        'user_id' => $artistUser->id
      ]);
      $artist->save();
    }


    $clientUser = new User([
      'name' => 'Client Name',
      'email' => 'client@email.com',
      'password' => 'password123',
      'type' => 'client',
      'avatar_url' => null
    ]);
    $clientUser->save();

    $client = new Client([
      'phone' => '11999999999',
      'user_id' => $clientUser->id
    ]);
    $client->save();


    for ($j = 0; $j < $numberOfUsers; $j++) {
      $user = new User([
        'name' => 'Client ' . $j . ' Name',
        'email' => 'client' . $j . '@email.com',
        'password' => 'password123',
        'type' => 'client',
        'avatar_url' => null
      ]);
      $user->save();

      $client = new Client([
        'phone' => '11999999999' . $j,
        'user_id' => $user->id
      ]);
      $client->save();
    }

    echo "Artists and Clients successfuly populated.\n";
  }
}
