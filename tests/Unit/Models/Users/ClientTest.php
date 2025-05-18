<?php 

namespace Tests\Unit\Models\Users;

use App\Models\Client;
use App\Models\User;
use Tests\TestCase;

class ClientTest extends TestCase{
   private User $user1;
   private User $user2;
   private Client $client;


   public function setUp(): void
   {
      $this->user1 = new User([
         'name' => 'Jose Henrique',
        'email' => 'jose@email.com',
        'encrypted_password' => '123henrique',
        'type' => 'client',
        'avatar_url' => null
      ]);
      $this->user1->save();

      $this->user2 = new User([
         'name' => 'Diogo Pedro',
        'email' => 'dio@email.com',
        'encrypted_password' => '123diogo',
        'type' => 'client',
        'avatar_url' => null
      ]);
      $this->user2->save();

      //CRIA UM CLIENTE ASSOCIADO AO user1
      $this->client = new Client([
         'phone' => '42998247591',
         'user_id' => $this->user1->id
      ]);
      $this->client->save();
   }

   public function test_shold_create_new_client(): void 
   {
      $this->assertCount(1, Client::all());
      $this->assertEquals($this->user1->id, $this->client->user_id);
   }

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
   
  
}