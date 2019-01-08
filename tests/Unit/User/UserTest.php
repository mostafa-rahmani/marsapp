<?php

namespace Tests\Unit\User;


use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Psy\Util\Str;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserTest extends TestCase
{
    use DatabaseMigrations;
    protected $users;
    protected $authUser;
    public function setUp()
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');
        $this->authUser = $user;
        $this->users = factory(User::class, 5)->create();

    }

    /** @test */
    public function client_can_get_a_user_by_sending_an_id()
    {
        $user = factory(User::class)->create();
        $response = $this->json('get', '/api/users/' . $user->id )->assertStatus(200);
        $user = [
            "id"    => $user->id,
            "username" => $user->username,
            "email" => $user->email,
            "bio"   => $user->bio,
            "blocked"   => $user->blocked,
            "instagram" => $user->instagram,
            "instagram_url" => $user->instagram ? 'https://www.instagram.com/' . $this->authUser->instagram  : null,
            "profile_image" => $user->profile_image ? image_url($this->authUser->profile_image, 'pi') : null,
            "profile_background"  => $user->profile_background ? image_url($this->authUser->profile_background, 'pg') : null,
        ];
        $responseData = [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "user returned successfully",
            "returned"  => "the requested user object",
            "data"      => [
                "user"      => $user,
                "users"     => null,

                "design"    => null,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            ]
        ];

        $response->assertJson($responseData);
    }

    /** @test */
    public function a_user_can_update_his_own_personal_data()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->json('POST', '/api/users/update', [
            'profile_image' => $file,
            'profile_background' => $file,
            "username" => "chloe.grace",
            "bio"      => "this my updated bio",
            "instagram" => "rhmostafa"
        ]);

        //? check if the files do exist
        Storage::disk('public')->assertExists(  $user->profile_image);
        Storage::disk('public')->assertExists( $user->profile_background);
        //? delete the files from disk
        Storage::disk('local')->delete([
            'public/' . $user->profile_background,
            'public/' . $user->profile_image
        ]);

        $responseData = [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "user updated successfully",
            "returned"  => "current logged in user",
            "data"      => [
//                "user" =>  $user->loadMissing(
//                    'seenComments', 'designs', 'following',
//                    'followers', 'likedDesigns', 'comments')->toArray(),
                "users"     => null,

                "design"    => null,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            ]
        ];
        $response->assertJson($responseData);
    }

    /** @test */
    public function a_logged_in_user_can_follow_another_user()
    {
        // when we follows bunch of users
        foreach ($this->users as $user){
            $response = $this->json('get', '/api/users/follow/' . $user->id);
        }

        // then we see followers are the same as followings
        $followings = $this->authUser->following()->get();
        $response->assertJson([
            "code" => "200"
        ]);
    }

    /** @test */
    public function client_can_get_the_followings_of_a_user()
    {

        foreach ($this->users as $user){
            $this->json('get', '/api/users/follow/' . $user->id);
        }
        $response = $this->json('get', '/api/users/followings/' . $this->authUser->id);

        $response->assertStatus(200)->assertJson([
            "code"  => "200"
        ]);
    }

    /** @test */
    public function client_can_get_the_followers_of_a_user()
    {

        foreach ($this->users as $user){
            $this->actingAs($user);
            $this->json('get', '/api/users/follow/' . $this->authUser->id);
        }
        $this->actingAs($this->authUser);

        $response = $this->json('get', '/api/users/followers/' . $this->authUser->id);

        $response->assertStatus(200)->assertJson([
            "code" => "200"
        ]);
    }

}











