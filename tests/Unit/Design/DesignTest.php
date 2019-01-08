<?php

namespace Tests\Unit;

use App\Design;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

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
     public function an_authenticated_user_can_like_a_design()
     {
         // given we have an authenticated user
         $user = $this->authUser;
         $design = $this->designs[1];
         // when the user likes a design
         $response = $this->json("get", "/api/designs/" . $design->id . "/like");

         //then we wants those designs in the user liked design
         $this->assertArraySubset($design->toArray(), $user->likedDesigns()->get()->toArray());
         $response->assertStatus(200)
             ->assertJson([
             "status" => "ok",
             "code" => "200",
             "message" => "you successfully liked design " . $design->id ,
             "returned" => "liked design and authenticated user",
             "data"  => [
                 "user"  => $user->toArray(),
                 "users" => null,
                 "design"    => $design->toArray(),
                 "designs"   => null,
                 "comment" => null,
                 "comments"  => null
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
        $response = $this->json("get", "/api/designs/". $design->id ."/dislike");
        // then we must receive the liked design and the user object
        $response->assertStatus(200)
            ->assertJson([
                "status" => "ok",
                "code" => "200",
                "message" => "you successfully disliked design " . $design->id ,
                "returned" => "disliked design and authenticated user",
                "data" => [
                    "user" => $user,
                    "design" => $design
                ]
            ]);
    }

    /** @test */
     public function client_can_request_for_a_design_by_id(){
         // given we have a design with the id of 21
         $design = factory(Design::class)->create(["id" => 21]);

         // when we request for the design with the id of 21
         $response = $this->json("get", "/api/designs/21" );

         // then we must receive the design
         $response->assertJson([
             "status"    => "ok",
             "code" => "200",
             "message"   => "design returned successfully",
             "returned"  => "requested design object",
             "data" => [
                 "design" => [
                     "id" => 21
                 ]
             ]
         ]);
     }

     /** @test */
     public function client_cannot_receive_the_blocked_design_by_id(){
         // given we have a design with the id of 21
         $design = factory(Design::class)->create(["id" => 21, "blocked" => 1]);

         // when we request for the design with the id of 21
         $response = $this->json("get", "/api/designs/21" );

         // then we must receive the design
         $response->assertStatus(403)->assertJson([
             "status"    => "error",
             "code" => "403",
             "message"   => "you can not access this design. it is blocked by the admins.",
             "returned"  => null,
             "data" => [
                 "design" => null
             ]
         ]);
     }

     /** @test
      * download method
      */

     /** @test */
     public function logged_in_user_can_receive_following_users_designs(){
         // given we have an authenticated user that follows multiple other users
         $users = factory(\App\User::class, 5)->create();
         $designs = [];
         $index = 51;
         foreach ($users as $user ){
             $design = factory(Design::class, 5)->create([
                 "user_id" => $user->id
             ]);
             array_push($designs, $design);
             $this->json("get", "/api/users/follow/" . $user->id);
         }
         //when we request for following designs
         $response = $this->json("get", "/api/designs/following/get");
         // then we receive followed users designs
         $response->assertStatus(200)->assertJson([
             "data" => []
         ]);
     }

    /** @test */
    public function client_can_request_a_list_of_designs_using_ids()
    {
        // given we have 4 design in our database
        $designs = factory(Design::class, 3)->create();

        // when we requested for designs using 3 ids
        $response = $this->json("post", "/api/designs/list", [
           "ids" => [
               $designs[0]->id, $designs[1]->id, $designs[2]->id
           ]
        ]);
        // then we must receive a list of 3 designs
        $response->assertStatus(200);
        // dd($response->json());

        $response->assertJson( [
          "current_page" => 1
        ]);
        // $response->assertJson([
        //
        //
        //       "id" => $designs[0]->id,
        //       "description" => $designs[0]->description,
        //       "image" => $designs[0]->image,
        //       "small_image" => $designs[0]->small_image,
        //       "original_width" =>  $designs[0]->original_width,
        //       "original_height" =>  $designs[0]->original_height,
        //       "is_download_allowed" =>  $designs[0]->is_download_allowed,
        //       "blocked" =>  $designs[0]->blocked,
        //       "user_id" => $designs[0]->user_id,
        //       "download_count" =>  $designs[0]->download_count,
        //       "like_count" => $designs[0]->like_count,
        //       "user" => $designs[0]->user(),
        //       "comments" =>  $designs[0]->comments,
        //       "download_users" =>  $designs[0]->download_users,
        //       "likes" => $designs[0]->likes,
        //
        //   ]
        // );



    }
}
