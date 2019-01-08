<?php

namespace Tests\Unit;

use App\Comment;
use App\Design;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{

    protected $authUser;
    protected $design;

    protected function setUp()
    {
        parent::setUp();
        $this->authUser = factory(User::class)->create();
        $this->actingAs($this->authUser, "api");
        $this->design = factory(Design::class)->create();
    }

    /** @test */
    public function client_can_create_a_comment()
    {

        // when we created a comment for this specific design
        $response = $this->json("post", "/api/comments/" . $this->design->id . "/create", [
           "content" => "a new comment from CommentTest"
        ]);
        // then we must receive the created comment in response
        $response->assertStatus(200)
            ->assertJson([
                "message"   => "comment created successfully",
                "returned"  => "the created comment object",
                "data"  => [
                    "comment"   => [
                        "content"   => "a new comment from CommentTest"
                    ]
                ]
            ]);
    }

    /** @test */
    public function client_can_get_a_comment_by_id()
    {
        // given we have a design and a comment for that
        factory(Comment::class)->create([
            "id" => 321,
            "content" => "a new comment from CommentTest"
        ]);
        //when we request for a comment using id
        $response = $this->json("get", "/api/comments/321");
        // then we receive the corresponding comment object
        $response->assertStatus(200)->assertJson([
            "status" => "ok",
            "code" => "200",
            "message"   => "comment returned successfully",
            "returned"  => "comment object",
            "data" => [
                "comment" => [
                    "content" => "a new comment from CommentTest"
                ]
            ]
        ]);

    }

    /** @test */
    public function client_can_update_his_comment()
    {
        // given we have a authenticated user who created a comment
        $comment = factory(Comment::class)->create([
            "id" => 432,
            "user_id" => $this->authUser
        ]);
        // when he/she requested to updated the created comment
        $response = $this->json("patch", "/api/comments/432/update", [
            "content" => "I updated this comment as a authenticated user"
        ]);
        // then he/she receives the updated comment obj
        $response->assertStatus(200)->assertJson([
            "data" => [
                "comment" => [
                    "content" => "I updated this comment as a authenticated user"
                ]
            ]
        ]);
    }

    /** @test */
    public function client_can_delete_her_comment()
    {
        $comment = factory(Comment::class)->create([
            "id" => 454,
            "user_id"   => $this->authUser->id
        ]);
        $response = $this->json("delete", "/api/comments/454/delete");
        $response->assertStatus(204)->assertJson([
            "status" => "ok",
            "code" => "204",
           "message" => "comment deleted successfully",
            "returned" => null
        ]);
    }
}
