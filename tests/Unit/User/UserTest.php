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

        $responseData = [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "user returned successfully",
            "returned"  => "the requested user object",
            "data"      => [
                "user"      => [
                    "id"        => $user->id,
                    "followers"    =>  $user->followers()->get()->toArray(),
                    "comments"      => $user->comments()->get()->toArray()
                ],
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
        $bu_profile_image = $user->profile_image;// user profile image before update
        $bu_profile_background = $user->profile_background;// user profile background before update

        $this->actingAs($user, 'api');
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->json('POST', '/api/users/update', [
            'profile_image' => $file,
            'profile_background' => $file,
            "username" => "chloe.grace",
            "bio"      => "this is my updated bio",
            "instagram" => "rhmostafa"
        ]);

        //? Assert the old files do not exist
        Storage::disk('public')->assertMissing( $bu_profile_background );
        Storage::disk('public')->assertMissing( $bu_profile_image );

        //? Assert the new files do exist
        Storage::disk('public')->assertExists(  $user->profile_image );
        Storage::disk('public')->assertExists( $user->profile_background );

//        //? delete the files from disk
//        Storage::disk('local')->delete([
//            'public/' . $user->profile_background,
//            'public/' . $user->profile_image
//        ]);

        $responseData = [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "user updated successfully",
            "returned"  => "current logged in user",
            "data"      => [
                "user" =>  [
                    "username"  => 'chloe.grace',
                    'bio'       => 'this is my updated bio',
                    "instagram" => "rhmostafa"
                ],
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
        // auth user can follow other users
        foreach ($this->users as $user){
            $response = $this->json('get', '/api/users/follow/' . $user->id);
            $response->assertStatus(200)
                ->assertJson([
                    'status'    => 'ok',
                    'code'      =>  '200',
                    'message'   => "you followed $user->username successfully ",
                    'returned'  => 'auth user and followed user',
                    'data'      => [
                        'user'      =>  null,
                        'users'     => [
                            [ // auth user
                                'following' =>  User::find($this->authUser->id)
                                    ->following()->get()->toArray()
                            ],
                            [ // currently followed user
                                'followers' => User::find($user->id)
                                    ->followers()->get()->toArray()
                            ]
                        ],
                        'design'   => null,
                        'designs'   => null,
                        'comment'   => null,
                        'comments'   => null,
                    ]
                ]);
        }
    }

    /** @test */
    public function an_authenticated_user_can_unfollow_the_followed_users_of_hers(){
        // given we have some users that auth user is following them
        $users = $this->users; $auth_user = $this->authUser;
        // when the auth user unfollow them
        foreach ($users as $user ){
            $response = $this->json('get', '/api/users/unfollow/' . $user->id);
            // then we must not see them in our followers array
            $followings = User::find($auth_user->id)->following()->get()->toArray();
            $response->assertStatus(200)
                    ->assertJson([
                        'status'    => 'ok',
                        'code'      => '200',
                        'data'      => [
                            'users' => [
                                [ 'following' => $followings ], // auth user
                                [ 'followers' => User::find($user->id)->followers()->get()->toArray() ] // subject_user
                            ]
                        ]
                    ]);
        }
    }

    /** @test */
    public function client_can_get_the_followings_of_a_user()
    {

        foreach ($this->users as $user){
            $this->json('get', '/api/users/follow/' . $user->id);
        }
        $response = $this->json('get', '/api/users/followings/' . $this->authUser->id);

        $response->assertStatus(200)->assertJson([
            'status'    => 'ok',
            "code"      => "200",
            'data'      => [
                'users' => User::find($this->authUser->id)->following()->get()->toArray()
            ]
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
            'status'    => 'ok',
            "code" => "200",
            'data' => [
                'users' => User::find($this->authUser->id)->followers()->get()->toArray()
            ]
        ]);
    }

}











