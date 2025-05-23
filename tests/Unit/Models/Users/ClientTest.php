<?php 

namespace Tests\Unit\Models\Users;

use App\Models\Client;
use App\Models\User;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\equalTo;

class ClientTest extends TestCase{
   private User $user_client;
   private Client $client;


   public function setUp(): void
   {
      parent::setUp();

      $this->user_client = new User([
         'name' => 'User_client',
         'email' => 'user@example.com',
         'password' => '123456',
         'password_confirmation' => '123456'
      ]);
      $this->user_client->save();

      $this->client = new Client([
        'phone' => '42999997826',
        'user_id' => $this->user_client->id
      ]);
      $this->client->save();
   }

   public function test_shold_create_new_client(): void 
   {
      $this->assertCount(1, Client::all());
      $this->assertEquals($this->user_client->id, $this->client->user_id);
   }

   public function test_should_return_all_clients(): void 
   {
      $user_client_2 = new User([
         'name' => 'User_client2',
         'email' => 'user2@example.com',
         'password' => '123456',
         'password_confirmation' => '123456'
      ]);
      $user_client_2->save();

      $client_2 = new Client([
         'phone' => '42996752016',
         'user_id' => $user_client_2->id
      ]);
      $client_2->save();

      
      $this->assertCount(2, Client::all());
   }

   public function test_should_validate_client_is_null(): void {
      $client = new Client([
         'phone' => '42996752016',
      ]);
      $client->save();

      $this->assertEquals(0 || null, $client->validates());
   }

   public function test_should_associate_client_to_user(): void {
      $user = new User([
         'name' => 'User_client',
         'email' => 'userTeste@example.com',
         'password' => '123456',
         'password_confirmation' => '123456'
      ]);
      $user->save();

      
      $client = $user->belongsTo('42998247591', $user->id);
      assertEquals($client ,$user->belongsTo('42998247591', $user->id));
      
   }
/* 
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