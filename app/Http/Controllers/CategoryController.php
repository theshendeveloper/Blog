<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{

    private $category;
    public function __construct(CategoryRepository $category)
    {
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $current_page = request()->get('page') ?: "1";
        $categories =  Cache::tags('categories')->rememberForever('categories-'.$current_page,function(){
            return $this->category->paginate(10);
        });
        return view('panel.categories.index',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $attributes=$request->validate([
            'name' => ["required","string","max:255","unique:categories"]
        ]);
        $this->category->create($attributes);
        session()->flash('status', [
            'type' => 'success',
            'message' =>'دسته‌بندی با موفقیت اضافه شد.'
        ]);
        return redirect()->route('categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @return View
     */
    public function edit(Category $category)
    {
        return view('panel.categories.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @return RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $attributes=$request->validate([
            'name' => ["required","string","max:255","unique:categories,name,$category->id"]
        ]);
        $this->category->update($category,$attributes);
        if ($category->wasChanged()){
            session()->flash('status', [
                'type' => 'success',
                'message' =>'دسته‌بندی با موفقیت آپدیت شد.'
            ]);        }
        return redirect(route('categories.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Category $category)
    {
        try {
            $this->category->delete($category);
        }
        catch (QueryException $e) {
            {
                session()->flash('status', [
                    'type' => 'error',
                    'message' =>'امکان حذف این دسته‌بندی به دلیل داشتن پست‌ وجود ندارد.'
                ]);
                return redirect()->route('categories.index');

            }
        }
        session()->flash('status', [
        'type' => 'success',
        'message' =>'دسته‌بندی با موفقیت حذف شد.'
        ]);
        return redirect()->route('categories.index');
    }
}
