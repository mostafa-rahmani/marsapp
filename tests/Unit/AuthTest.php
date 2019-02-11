<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseMigrations;
    public function setUp()
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');
        $this->authUser = $user;
        $this->users = factory(User::class, 5)->create();
    }

    /** @test */
    public function client_can_request_to_change_the_password()
    {
        $user = factory(User::class)->create();
        $response = $this->json('post', '/api/auth/password/create', [
            'email' =>  $user->email
        ]);
        // if send a valid email address
        $response->assertStatus(200)->assertJson([
            'status'    =>  'ok',
            'code'      =>  200,
            'message' => 'ایمیل بازیابی رمز عبور برای شما ارسال شد. ( ایمیل ممکن است در پوشه ایمیل شما ذخیره شده شود ) ',
            'returned'  =>  null,
            'data'  =>  null
        ]);
        // if the email address was not found
        $response = $this->json('post', '/api/auth/password/create', [
            'email' =>  'unregistered@email.com'
        ]);
        $response->assertStatus(404)->assertJson([
            'status'    =>  'error',
            'code'      =>  404,
            'message' => 'ایمیل مورد نظر پیدا نشد',
            'returned'  =>  null,
            'data'  =>  null
        ]);
    }
}
