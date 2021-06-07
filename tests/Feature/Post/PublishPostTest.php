<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishPostTest extends TestCase
{
    use RefreshDatabase;

    private $post;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->post = $this->createPost();

    }
    /** @test */
    public function only_an_admin_or_moderator_can_see_the_publish_buttons(){
        $user = $this->createUserAndAssignRole('WhereNotIn',['Admin','Moderator']);
        $this->be($user);
        $this->get(route('posts.index'))->assertOk()
            ->assertDontSee('Publish')
            ->assertDontSee('Unpublish');
    }

    //Publish
    /** @test */
    public function only_an_admin_or_moderator_can_publish_a_post()
    {
        $user = $this->createUserAndAssignRole('whereNotIn',['Admin','Moderator']);
        $this->be($user);

        $this->post(route('post.publish.store',$this->post))->assertForbidden();
        $this->assertFalse($this->post->refresh()->is_published);
    }

    /** @test */
    public function an_admin_can_publish_a_post()
    {
        $user = $this->createUserAndAssignRole('where','Admin');
        $this->be($user);

        $this->post(route('post.publish.store',$this->post))->assertRedirect();
        $this->assertTrue($this->post->refresh()->is_published);
    }
    /** @test */
    public function a_moderator_can_publish_a_post()
    {
        $user = $this->createUserAndAssignRole('where','Moderator');
        $this->be($user);

        $this->post(route('post.publish.store',$this->post))->assertRedirect();
        $this->assertTrue($this->post->refresh()->is_published);
    }

    // Unpublish

    /** @test */
    public function only_an_admin_or_moderator_can_unpublish_a_post()
    {
        $post = $this->createPost(['is_published'=>true]);
        $user = $this->createUserAndAssignRole('whereNotIn',['Admin','Moderator']);
        $this->be($user);
        $this->delete(route('post.publish.destroy',$post))->assertForbidden();
        $this->assertTrue($post->refresh()->is_published);
    }

    /** @test */
    public function an_admin_can_unpublish_a_post()
    {
        $post = $this->createPost(['is_published'=>true]);
        $user = $this->createUserAndAssignRole('where','Admin');
        $this->be($user);

        $this->delete(route('post.publish.destroy',$post))->assertRedirect();
        $this->assertFalse($post->refresh()->is_published);
    }
    /** @test */
    public function a_moderator_can_unpublish_a_post()
    {
        $post = $this->createPost(['is_published'=>true]);
        $user = $this->createUserAndAssignRole('where','Moderator');
        $this->be($user);

        $this->delete(route('post.publish.destroy',$post))->assertRedirect();
        $this->assertFalse($post->refresh()->is_published);
    }


    protected function createPost($overrides = [])
    {
        return create(Post::class,array_merge(['is_published' => false],$overrides));
    }
}
