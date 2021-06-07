<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ManageCommentsTest extends TestCase
{
    use RefreshDatabase;

    private $post,$comment;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        Auth::logout();
        $this->post = create(Post::class);
        $this->comment = $this->createComment();


    }
    /** @test */
    public function only_an_admin_or_moderator_can_view_the_comments_page()
    {
        $user = $this->createUserAndAssignRole('whereNotIn',['Admin','Moderator']);
        $this->be($user);

        $this->get(route('comments.index'))->assertForbidden();

    }
    /** @test */
    public function an_admin_can_view_the_comments_page()
    {
        $user = $this->createUserAndAssignRole('where','Admin');
        $this->be($user);

        $this->get(route('comments.index'))->assertOk()->assertSee($this->comment->author);


    }

    /** @test */
    public function a_moderator_can_view_the_comments_page()
    {
        $user = $this->createUserAndAssignRole('where','Moderator');
        $this->be($user);

        $this->get(route('comments.index'))->assertOk()->assertSee($this->comment->author);


    }
    // Publish
    /** @test */
    public function only_an_admin_or_moderator_can_publish_a_comment()
    {
        $user = $this->createUserAndAssignRole('whereNotIn',['Admin','Moderator']);
        $this->be($user);

        $this->post(route('comment.publish.store',$this->comment))->assertForbidden();
        $this->assertFalse($this->comment->refresh()->is_published);
    }

    /** @test */
    public function an_admin_can_publish_a_comment()
    {
        $user = $this->createUserAndAssignRole('where','Admin');
        $this->be($user);

        $this->post(route('comment.publish.store',$this->comment))->assertRedirect();
        $this->assertTrue($this->comment->refresh()->is_published);
    }
    /** @test */
    public function a_moderator_can_publish_a_comment()
    {
        $user = $this->createUserAndAssignRole('where','Moderator');
        $this->be($user);

        $this->post(route('comment.publish.store',$this->comment))->assertRedirect();
        $this->assertTrue($this->comment->refresh()->is_published);
    }

    // Unpublish

    /** @test */
    public function only_an_admin_or_moderator_can_unpublish_a_comment()
    {
        $comment = $this->createComment();
        $comment->publish();
        $user = $this->createUserAndAssignRole('whereNotIn',['Admin','Moderator']);
        $this->be($user);
        $this->delete(route('comment.publish.destroy',$comment))->assertForbidden();
        $this->assertTrue($comment->refresh()->is_published);
    }

    /** @test */
    public function an_admin_can_unpublish_a_comment()
    {
        $comment = $this->createComment();
        $comment->publish();
        $user = $this->createUserAndAssignRole('where','Admin');
        $this->be($user);

        $this->delete(route('comment.publish.destroy',$comment))->assertRedirect();
        $this->assertFalse($comment->refresh()->is_published);
    }
    /** @test */
    public function a_moderator_can_unpublish_a_comment()
    {
        $comment = $this->createComment();
        $comment->publish();
        $user = $this->createUserAndAssignRole('where','Moderator');
        $this->be($user);

        $this->delete(route('comment.publish.destroy',$comment))->assertRedirect();
        $this->assertFalse($comment->refresh()->is_published);
    }
    // Delete
    /** @test */
    public function only_an_admin_or_moderator_can_delete_a_comment()
    {
        $user = $this->createUserAndAssignRole('whereNotIn',['Admin','Moderator']);
        $this->be($user);
        $this->assertEquals(1,Comment::count());
        $this->delete(route('comments.destroy',$this->comment))->assertForbidden();
        $this->assertEquals(1,Comment::count());
    }

    /** @test */
    public function an_admin_can_delete_a_comment()
    {
        $user = $this->createUserAndAssignRole('where','Admin');
        $this->be($user);

        $this->assertEquals(1,Comment::count());
        $this->delete(route('comments.destroy',$this->comment))->assertRedirect();
        $this->assertEquals(0,Comment::count());
    }
    /** @test */
    public function a_moderator_can_delete_a_comment()
    {
        $user = $this->createUserAndAssignRole('where','Moderator');
        $this->be($user);

        $this->assertEquals(1,Comment::count());
        $this->delete(route('comments.destroy',$this->comment))->assertRedirect();
        $this->assertEquals(0,Comment::count());
    }
    protected function createComment($overrides = [])
    {
        return create(Comment::class,array_merge(['is_published' => false],$overrides));
    }
}
