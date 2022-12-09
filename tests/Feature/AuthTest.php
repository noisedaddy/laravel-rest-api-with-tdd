<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Laravel\Passport\PersonalAccessClient;
use Laravel\Passport\PersonalAccessTokenResult;
use Tests\TestCase;
use Tests\Traits\PersonalClient;

class AuthTest extends TestCase
{
    use RefreshDatabase, PersonalClient;

    /**
     * @return void
     */
    public function test_can_create_personal_access_token() {

        Passport::$hashesClientSecrets = false;

        $this->artisan(
            'passport:client',
            ['--name' => config('app.name'), '--personal' => null]
        )->assertSuccessful();

        $this->assertDatabaseCount(PersonalAccessClient::class,1);

    }

    /**
     *
     * @return void
     */
    public function test_can_issue_a_personal_access_token()
    {
        $this->createPersonalClient();
        $user = User::factory()->create()->createToken('test');
        $this->assertInstanceOf(PersonalAccessTokenResult::class, $user);
        $this->assertObjectHasAttribute('accessToken', $user);
        $this->assertObjectHasAttribute('token', $user);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register()
    {
        $this->createPersonalClient();

        $response = $this->json('POST', '/api/register', [
            'name' => $name = 'Test',
            'email' => $email = time().'test@example.com',
            'password' => $password = '123456789',
        ]);

        Log::info(1, [
            $response->getContent()
        ]);

        $response->assertStatus(200);

        $this->assertArrayHasKey('access_token', $response->json());
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login()
    {
        $this->createPersonalClient();

       User::create([
           'name'=>'Test',
           'email'=> $email = time().'@example.com',
           'password'=>$password = bcrypt('123456789')
       ]);

        $response = $this->json('POST', 'api/login', [
            'email' => $email,
            'password' => $password,
        ]);

        Log::info(1, [
            $response->getContent()
        ]);

        $response->assertStatus(200);

//        $this->assertArrayHasKey('access_token', $response->json());

        User::where('email', 'test@gmail.com')->delete();

    }

}
