<?php 

namespace Tests\Unit\Models\Users;

use App\Models\Client;
use App\Models\User;
use Tests\TestCase;

class ClientTest extends TestCase{
   private User $user_client;
   private Client $client;


   public function setUp(): void
   {
      $this->user_client = new User([
         'name' => 'User_client',
         'email' => 'artist@example.com',
         'password' => '123456',
         'password_confirmation' => '123456'
      ]);
      $this->user_client->save();

      $this->client = new Client([
        'bio' => 'me siga',
        'portfolio_url' => 'hhtps://teste',
        'ai_detection_count' => 0,
        'user_id' => $this->user_client->id
      ]);
      $this->client->save();
   }

   public function test_shold_create_new_client(): void 
   {
      $this->assertCount(1, Client::all());
      $this->assertEquals($this->user_client->id, $this->client->user_id);
   }
/* 
   public function test_should_return_all_clients(): void 
   {
      $newClient = new Client([
         'phone' => '42996752016',
         'user_id' => $this->user2->id
      ]);
      $newClient->save();

      
      $this->assertCount(2, Client::all());
   }

   public function test_destroy_should_remove_client(): void 
   {
      $this->client->destroy();
      $this->assertCount(0, Client::all());
   }

   public function test_client_associated_as_user(): void 
   {
      $this->assertEquals($this->user1->id, $this->client->user()->get()->id);
   }
*/
  
}