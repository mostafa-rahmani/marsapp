<?php

namespace Tests\Feature;

use App\User;
use App\Design;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use  Illuminate\Http\UploadedFile;

class SimpleTest extends TestCase
{
    use DatabaseMigrations;
    protected $authUser;
    public function setUp()
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');
        $this->authUser = $user;
    }
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function can_get_background_and_image_url_in_user_object()
    {
        //given we have a authenticated user
        $image = UploadedFile::fake()->image('avatar.jpg');
        $design = factory(Design::class)->create([
            'user_id' => $this->authUser->id
        ]);
        // when we  set the background_image and profile_image property
        $response = $this->json('post', '/api/users/update' , [
            'profile_image' => $image,
            'profile_background' => $image
        ]);
        // then we want to see Background_image and profile_image as a url
        $response = $this->json('get', '/api/designs/' . $design->id);
        dd($response->json());
        $profile_image = $response->json()->data;
        $profile_background = '';
        if(true){
            $this->assertTrue();
        }else{
            $this->assertFalse();
        }
    }
}
