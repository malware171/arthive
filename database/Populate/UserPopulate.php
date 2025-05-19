<?php

namespace Database\Populate;

use App\Models\User;
use App\Models\Artist;
use App\Models\Client;

class UserPopulate
{
  public static function populate(): void
  {
    self::createArtist('Artist', 'artist@email.com', '12345', "Artist bio");

    $numberOfUsers = 3;

    for ($i = 0; $i < $numberOfUsers; $i++) {
      self::createArtist(
        'Artist ' . $i . ' Name',
        'artist' . $i . '@email.com',
        'password123',
        "Artist's bio " . $i
      );
    }

    self::createClient('Client', 'client@email.com', '12345', '11999999999');

    for ($j = 0; $j < $numberOfUsers; $j++) {
      self::createClient(
        'Client ' . $j . ' Name',
        'client' . $j . '@email.com',
        'password123',
        '11999999999' . $j
      );
    }
  }

  private static function createArtist(string $name, string $email, string $password, string $bio): void
  {
    $user = new User();
    $user->name = $name;
    $user->email = $email;
    $user->password = $password;
    $user->type = 'artist';
    $user->avatar_url = null;
    $user->save();

    $artist = new Artist([
      'bio' => $bio,
      'portfolio_url' => '',
      'ai_detection_count' => 0,
      'user_id' => $user->id
    ]);
    $artist->save();
  }

  private static function createClient(string $name, string $email, string $password, string $phone): void
  {
    $user = new User();
    $user->name = $name;
    $user->email = $email;
    $user->password = $password;
    $user->type = 'client';
    $user->avatar_url = null;
    $user->save();

    $client = new Client([
      'phone' => $phone,
      'user_id' => $user->id
    ]);
    $client->save();
  }
}
