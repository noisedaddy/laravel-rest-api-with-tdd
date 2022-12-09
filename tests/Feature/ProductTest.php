<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Tests\Traits\PersonalClient;

class ProductTest extends TestCase
{
    use RefreshDatabase, PersonalClient;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function authenticate()
    {
        $this->createPersonalClient();

//        $user = User::factory()->create();

        $user = User::create([
            'name' => 'test',
            'email' => rand(12345,678910).'test@gmail.com',
            'password' => Hash::make('secret9874'),
        ]);

        if (!auth()->attempt(['email'=>$user->email, 'password'=>'secret9874'])) {
            return response(['message' => 'Login credentials are invaild']);
        }

        return $accessToken = auth()->user()->createToken('authToken')->accessToken;

    }

    /**
     * test create product.
     *
     * @return void
     */
    public function test_create_product()
    {
        $this->createPersonalClient();

        $token = $this->authenticate();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST','api/product',[
            'name' => 'Test product',
            'sku' => 'test-sku',
            'upc' => 'test-upc'
        ]);

        //Write the response in laravel.log
        Log::info(1, [
            $response->getContent()
        ]);

        $response->assertStatus(200);
    }


    /**
     * test update product.
     *
     * @return void
     */
    public function test_update_product()
    {
        $this->createPersonalClient();

        $token = $this->authenticate();

        $product = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST','api/product',[
            'name' => 'Test product',
            'sku' => 'test-sku',
            'upc' => 'test-upc'
        ]);

        $product = json_decode($product->getContent());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('PUT','api/product/'.$product->product->id,[
            'name' => 'Test product111',
            'sku' => 'test-sku',
            'upc' => 'test-upc'
        ]);

        //Write the response in laravel.log
        Log::info(1, [
            $response->getContent()
        ]);

        $response->assertStatus(200);
    }

    /**
     * test find product.
     *
     * @return void
     */
    public function test_find_product()
    {
        $this->createPersonalClient();
        $token = $this->authenticate();

        $product = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST','api/product',[
            'name' => 'Test product',
            'sku' => 'test-sku',
            'upc' => 'test-upc'
        ]);

        $product = json_decode($product->getContent());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET','api/product/'.$product->product->id);

        //Write the response in laravel.log
        Log::info(1, [$response->getContent()]);

        $response->assertStatus(200);
    }

    /**
     * test get all products.
     *
     * @return void
     */
    public function test_get_all_product()
    {
        $this->createPersonalClient();

        $token = $this->authenticate();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET','api/product');

        //Write the response in laravel.log
        Log::info(1, [
            $response->getContent()
        ]);

        $response->assertStatus(200);
    }

    /**
     * test delete products.
     *
     * @return void
     */
    public function test_delete_product()
    {
        $this->createPersonalClient();

        $token = $this->authenticate();

        $product = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST','api/product',[
            'name' => 'Test product',
            'sku' => 'test-sku',
            'upc' => 'test-upc'
        ]);

        $product = json_decode($product->getContent());


        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('DELETE','api/product/'.$product->product->id);

        //Write the response in laravel.log
        Log::info(1, [$response->getContent()]);

        $response->assertStatus(200);
    }








}
