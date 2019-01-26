<?php

namespace Tests\Unit;

use App\Design;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use  Illuminate\Http\UploadedFile;

class DesignTest extends TestCase
{
    use DatabaseMigrations;
    protected $authUser;
    protected $designs;
    protected $users;

    public function setUp()
    {
        parent::setUp();
        // given we have an authenticated user and 5 created design by 5 other random users
        $this->authUser = factory(User::class)->create();
        $this->users = factory(User::class, 5)->create();
        $this->designs = factory(Design::class, 5)->create();
        $this->actingAs($this->authUser, 'api');
    }

    /** @test */
    public function client_can_create_design()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->json('post', '/api/designs/create', [
            'description'   => 'this is my description',
            'image' =>  $file,
            'is_download_allowed'   => true
        ]);
        $response->assertStatus(200)
                ->assertJson([
                    'data'  => [
                        'user'  => [
                            'designs'   => [
                                [
                                    'description'   => 'this is my description',
                                    'is_download_allowed'   => '1',
                                    'likes' => []
                                ]
                            ]
                        ],
                        'design'    => [
                            'description'   => 'this is my description',
                            'is_download_allowed'   => '1'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function client_can_request_for_designs_with_pagination_response(){
        // given we have some designs
        $designs = $this->designs;
        // when we request for all designs
        $response = $this->json('get', '/api/designs');
        // then we receive paginated designs in response
        $response->assertStatus(200);
    }

    /** @test */
    public function an_authenticated_user_can_like_a_design()
    {
        // given we have an authenticated user
        $user = $this->authUser;
        $design = $this->designs[1];
        // when the user likes a design
        $response = $this->json("get", "/api/designs/like/" . $design->id );

        //then we wants those designs in the user liked design
        $response->assertStatus(200)
            ->assertJson([
                "status"    => "ok",
                "code" => "200",
                'data' => [
                    'user'      =>  [
                        'liked_designs'     => User::find($user->id)->likedDesigns()->get()->toArray()
                    ],
                    'users'     => null,
                    'design'    => [
                        "likes"   => Design::find($design->id)->likes()->get()->toArray()
                    ],
                    'designs'   => null,
                    'comment'   => null,
                    'comments'  => null
                ]
            ]);

    }

    /** @test */
    public function an_authenticated_user_can_dislike_a_design()
    {
        // given we have designs
        $user = $this->authUser;
        $design = $this->designs[0];
        // when authenticated user dislikes a design
        $response = $this->json("get", "/api/designs/dislike/". $design->id);
        // then we must receive the liked design and the user object
        $response->assertStatus(200)
            ->assertJson([
                "status"    => "ok",
                "code" => "200",
                'data' => [
                    'user'      =>  [
                        'liked_designs'     => User::find($user->id)->likedDesigns()->get()->toArray()
                    ],
                    'users'     => null,
                    'design'    => [
                        "likes"       => Design::find($design->id)->likes()->get()->toArray()
                    ],
                    'designs'   => null,
                    'comment'   => null,
                    'comments'  => null
                ]
            ]);
    }

    /** @test */
    public function client_can_request_for_a_design_by_id_and_can_not_request_for_blocked_one(){
        // when we request for a design
        $design = $this->designs[3];
        $response = $this->json("get", "/api/designs/" . $design->id );
        // then we must receive the design
        $response->assertJson([
            "status"    => "ok",
            "code" => "200",
            "message"   => "design returned successfully",
            "returned"  => "requested design object",
            "data" => [
                "design" => [
                    "id" => $design->id
                ]
            ]
        ]);
        // now if we block this design
        $design->blocked = true;
        $design->save();
        // then we must not be able to receive it in response
        $response = $this->json("get", "/api/designs/" . $design->id );
        $response->assertStatus(403)->assertJson([
            "status"    => "error",
            "code" => "403",
            "message"   => "you can not access this design. it is blocked by the admins.",
            "returned"  => null,
            "data" => [
                "design" => null
            ]
        ]);
        // when we send a wrong id
        $response = $this->json('get', '/api/designs/4322');
        // then we must receive an error
        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'code'  =>  '404',
                'message'   => 'design not found',
                'returned'  => null,
                'data'      => [
                    'user'  => null,
                    'users' => null,
                    'design'    => null,
                    'designs'   => null,
                    'comment'    => null,
                    'comments'   => null
                ]
            ]);
    }

    /** @test */
    public function logged_in_user_can_receive_following_users_designs(){
        // given we have an authenticated user that follows multiple other users
        $users = factory(User::class, 2)->create();

        foreach ($users as $user ){
            factory(Design::class, 2)->create([
                "user_id" => $user->id
            ]);
            $this->authUser->following()->attach($user);
        }
        //when we request for following designs
        $response = $this->json("get", "/api/designs/following/get");
        // then we receive followed users designsWW
        $response->assertStatus(200)->assertJson([
            'current_page' => '1',
            'data' => array_merge(
                $users[1]->designs()->get()->toArray(),
                $users[0]->designs()->get()->toArray()
            )
        ]);
    }

    /** @test */
    public function client_can_request_a_list_of_designs_using_ids()
    {
        // given we have 4 design in our database
        $designs = $this->designs;
        // when we requested for designs using 3 ids
        $response = $this->json("post", "/api/designs/list", [
            "ids" => [
                $designs[0]->id, $designs[1]->id
            ]
        ]);
        // then we must receive a list of 3 designs
        $response->assertStatus(200);
        $response->assertJson( [
            'current_page' => "1"
        ]);
    }
}
