<?php

namespace Tests\Feature\Category;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    /** @test */
    public function the_user_is_not_logged_in()
    {
        Auth::logout();
        $this->createCategory()->assertRedirect('/adwise_panel/login');
        $this->get(route('categories.index'))->assertRedirect('/adwise_panel/login');

        $this->assertGuest();

    }
    /** @test */
    public function the_user_does_not_have_authority_to_see_the_categories_page()
    {
        $user = $this->createUserAndAssignRole('whereNotIn');
        $this->be($user);
        $this->get(route('categories.index'))->assertForbidden();
    }

    /** @test */
    public function the_user_can_see_the_categories()
    {

        $user = $this->createUserAndAssignRole();
        $this->be($user);
        $this->createCategory()
            ->assertRedirect(route('categories.index'));
        $this->get(route('categories.index'))->assertSee(Category::first()->name);
    }

    /** @test */
    public function the_user_does_not_have_authority_to_create_a_category()
    {
        $user = $this->createUserAndAssignRole('whereNotIn');
        $this->be($user);
        $this->createCategory()->assertForbidden();
    }
    /** @test */
    public function the_category_is_created_by_writer_successfully()
    {
        $user = $this->createUserAndAssignRole('where', 'Writer');
        $this->be($user);
        $this->createCategory()->assertRedirect(route('categories.index'));
        $this->assertEquals(1,Category::count());

    }

    /** @test */
    public function the_category_is_created_by_admin_successfully()
    {
        $user = $this->createUserAndAssignRole('where', 'Admin');
        $this->be($user);
        $this->createCategory()->assertRedirect(route('categories.index'));
        $this->assertEquals(1,Category::count());

    }

    //Validation Tests


    /** @test */
    public function the_category_must_have_name()
    {
        $this->attributeValidation('name');
    }


    /** @test */
    public function the_name_must_be_unique()
    {
        $category = create(Category::class);
        $this->attributeValidation('name',$category->name);
    }

    protected function attributeValidation($attribute, $value = null)
    {
        $this->createCategory([$attribute => $value])->assertSessionHasErrors($attribute);
    }

    protected function createCategory($overrides = [])
    {
        return $this->post(route('categories.store'), $this->validData($overrides));
    }

    protected function validData($attributes=[]){
        $category = make(Category::class, $attributes)->toArray();
        return $category;
    }
}
