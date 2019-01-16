<?php

namespace Tests\Unit;

use App\Comment;
use App\Design;
use App\User;
use App\Http\Respources\User as UserRespource;
use Tests\TestCase;

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
    public function client_can_create_a_comment_for_a_design(){
        // ===== given we have a design
        $design = $this->design;
        // ===== when we create a comment fo this design
        $response = $this->json('post', '/api/comments/'. $design->id . '/create', [
            'content'   => 'this is comment is coming from CommentTest file'
        ]);
        // ====== then we must receive comment, design and user in response
        $response->assertStatus(200)->json([
            'status'    => 'ok',
            'code'      => '200',
            'data'      => [
                'user'  => \App\Http\Resources\User::make(User::find($this->authUser->id))->resolve(),
                'design'    => Design::find( $design->id )->toArray(),
                'comment'   => [
                    'content'   => 'this is comment is coming from CommentTest file'
                ]
            ]
        ]);

        // ===== when we send a wrong design id
        $response = $this->json('post', '/api/comments/'. '312412' . '/create', [
            'content'   => 'this is comment is coming from CommentTest file'
        ]);
        // ===== then we must receive a 404 error
        $response->assertStatus(404)->assertJson([
            'status'    =>  'error',
            'code'      =>  '404',
            'message'   =>  'Design Not Found'
        ]);

        // ===== when we send empty content for the comment
        $response = $this->json('post', '/api/comments/'. '312412' . '/create', [
            'content'   => ' '
        ]);
        // ===== then it must return and 400 error
        $response->assertStatus(400)->assertJson([
            'status'    =>  'error',
            'code'      =>  '400',
            'message'   =>  'the content of the comment is required and must be String.',
            'returned'  => null
        ]);
    }

    /** @test */
    public function client_can_receive_a_comment_by_sending_an_id(){
        // given we have a comment OBJ
        $comment = factory(Comment::class)->create([
            'content'   =>  'this is a brand new comment.'
        ]);
        // when we send the comment id with the request
        $response = $this->json('get', '/api/comments/' . $comment->id);
        // then we must receive the comment
        $response->assertStatus(200)->assertJson([
            'status'    =>  'ok', 'code'      =>  '200',
            'data'      =>  [
                'comment'   =>  [ 'content'   =>  'this is a brand new comment.' ]
            ]
        ]);
        //  ======= =====================
        //when we send a wrong id
        $response = $this->json('get', '/api/comments/' . '423342');
        //then we must receive 404 error
        $response->assertStatus(404)->assertJson([
            'status'    =>  'error', 'code' => '404', 'message' =>  'Comment Not Found!',
            'data'      =>  [
                'comment' => null
            ]
        ]);
    }

    /** @test */
    public function client_can_update_his_own_comment(){
        //given we have a comment
        $design = $this->design;
        $user = $this->authUser;
        $comment = factory(Comment::class)->create([
            'design_id' =>  $design->id,
            'user_id'  => $user->id,
            'content'   =>  'this is a brand new comment in order to us to update it'
        ]);
        // when we send empty values
        $response = $this->json('patch','/api/comments/' . $comment->id . '/update', [
            'content'   => ' ',
            'seen'      =>  ' '
        ]);
        // then we must receive 400 error
        $response->assertStatus(400)->assertJson([
            'status' => 'error', 'code' => '400',
            'message' => 'content and seen fields must not be empty. content must be String and seen must be Boolean'
        ]);

        // when we send a wrong comment id
        $response = $this->json('patch','/api/comments/' . '44324' . '/update', [
            'content'   => 'updated comment',
            'seen'      =>  true
        ]);
        // then we must receive 404 error
        $response->assertStatus(404)->assertJson([
            'status' => 'error', 'code' => '404',
            'message'   =>  'Comment Not Found!', 'returned' => null
        ]);
    }

    /** @test */
    public function client_can_delete_his_own_comment(){
        //given we created a comment
        $design = $this->design;
        $user = $this->authUser;
        $comment = factory(Comment::class)->create([
            'design_id' =>  $design->id,
            'user_id'  => $user->id,
            'content'   =>  'this is a brand new comment in order to us to delete it'
        ]);
        //when we requested to delete the comment
        $response = $this->json('delete', '/api/comments/' . $comment->id . '/delete');
        //then we must receive 204 response
        $response->assertStatus(204);
    }
}
