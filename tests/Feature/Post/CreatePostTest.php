<?php

namespace Tests\Feature\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreatePostTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    /** @test */
    public function the_user_is_not_logged_in()
    {
        Auth::logout();
        $data = $this->createPost()->toArray();
        $this->post(route('posts.store'),$data)->assertRedirect('/adwise_panel/login');
        $this->get(route('posts.create'))->assertRedirect('/adwise_panel/login');

        $this->assertGuest();

    }
    /** @test */
    public function the_user_does_not_have_authority_to_see_the_create_page()
    {
        $user = $this->createUserAndAssignRole('whereNotIn');
        $this->actingAs($user)->get(route('posts.create'))->assertForbidden();
    }
    /** @test */
    public function the_user_does_not_have_authority_to_create_a_post()
    {
        $user = $this->createUserAndAssignRole('whereNotIn');
        $data = $this->createPost()->toArray();
        $this->actingAs($user)->post(route('posts.store'),$data)->assertForbidden();
    }
    /** @test */
    public function the_post_is_created_by_writer_successfully()
    {
        $user = $this->createUserAndAssignRole('where','Writer');
        Storage::fake();
        $post = $this->createPost();
        $this->actingAs($user)->post(route('posts.store'),$post->toArray())->assertRedirect(route('posts.show',Post::first()));
        $path = 'images/banners/'.$post->banner->hashName();

        $post = Post::first();
        $post->publish();
        $this->assertEquals(url($path),$post->banner);
        Storage::disk()->assertExists($path);
        $this->assertEquals(1,Post::count());

        $categories = $post->categories->pluck('id')->toArray();
        $tags = $post->tags->pluck('name')->toArray();
        $this->assertContains(1,$categories);
        $this->assertContains(2,$categories);
        $this->assertContains('test1',$tags);
        $this->assertContains('test2',$tags);
        $this->get(route('posts.show',Post::first()))->assertSee($post->title);
    }

    /** @test */
    public function the_post_is_created_by_admin_successfully()
    {
        $user = $this->createUserAndAssignRole('where','Writer');
        Storage::fake();
        $post = $this->createPost();
        $this->actingAs($user)->post(route('posts.store'),$post->toArray())->assertRedirect(route('posts.show',Post::first()));
        $path = 'images/banners/'.$post->banner->hashName();

        $post = Post::first();
        $post->publish();

        $this->assertEquals(url($path),$post->banner);
        Storage::disk()->assertExists($path);
        $this->assertEquals(1,Post::count());

        $categories = $post->categories->pluck('id')->toArray();
        $tags = $post->tags->pluck('name')->toArray();
        $this->assertContains(1,$categories);
        $this->assertContains(2,$categories);
        $this->assertContains('test1',$tags);
        $this->assertContains('test2',$tags);
        $this->get(route('posts.show',Post::first()))->assertSee($post->title);
    }
    /** @test */
    public function the_content_editor_can_upload_image()
    {
        Storage::fake();
        $image = UploadedFile::fake()->image('test.png');
        $this->post(route('editor.upload'),['file' => $image])->assertOk();
        $path = 'images/editor-uploads/'.time().'test.png';
        Storage::disk()->assertExists($path);

    }
    // Validation Tests

    /** @test */
    public function the_categories_must_be_an_array()
    {
        $this->attributeValidation('categories',$this->faker->word);
    }
    /** @test */
    public function the_categories_must_exist_in_the_database()
    {
        $this->attributeValidation('categories',[12,25]);
    }

    /** @test */
    public function the_banner_must_be_an_image()
    {
        $this->attributeValidation('banner',UploadedFile::fake()->create('test.pdf'));
    }

    /** @test */
    public function the_banner_maximum_size()
    {
        $this->attributeValidation('banner',UploadedFile::fake()->image('test.png')->size(6000));
    }

    /** @test */
    public function the_summary_maximum_words()
    {
        $this->attributeValidation('summary',implode(' ',$this->faker->words(16)));
    }


    /** @test */
    public function the_slug_must_be_unique()
    {
        $user = create(User::class);
        $post = create(Post::class,['author_id'=>$user->id]);
        $this->attributeValidation('slug',$post->slug);
    }

    /** @test */
    public function the_post_must_have_title()
    {
        $this->attributeValidation('title');
    }

    /** @test */
    public function the_post_must_have_content()
    {

        $this->attributeValidation('content');

    }

    protected function attributeValidation($attribute,$value = null){
        $post = $this->createPost([$attribute => $value])->toArray();
        $this->post(route('posts.store'),$post)->assertSessionHasErrors($attribute);
    }

    protected function createPost($overrides = []){
        return make(Post::class,array_merge($this->additionalData(),$overrides));

    }
    protected function additionalData(){
        factory(Category::class,2)->create();
        return [
            'categories' => [1,2],
            'tags' => ['test1','test2'],
            'banner' => UploadedFile::fake()->image('test.jpg'),
            'author_id' => null,
        ];

    }
}
