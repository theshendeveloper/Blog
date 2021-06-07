<?php

namespace App\Http\Controllers;

use App\Events\PostDeleted;
use App\Http\Requests\Panel\PostRequest;
use App\Models\Post;
use App\Repositories\PostRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    private $post;

    public function __construct(PostRepository $post)
    {
        $this->post = $post;
        $this->middleware('can:Write-Post')->only(['create','store','destroy']);
        $this->middleware('can:Edit-Post')->only(['edit','update']);
    }


    protected function uploadBanner($banner){
        $banner_name = $banner->hashName();
        $banner->store('images/banners/');
        return $banner_name;
    }
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $can_publish = Gate::allows('Publish-Post');
        $current_page = request()->get('page') ?: "1";
        $posts = Cache::tags('panel-posts')->rememberForever('panel-posts-'.$current_page, function (){
            return $this->post->getWith('author.roles');
        });
        return view('panel.posts.index',compact('posts','can_publish'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('panel.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostRequest $request
     * @return RedirectResponse
     */
    public function store(PostRequest $request)
    {
        $attributes = $request->validated();
        $attributes['banner'] = url("/images/banners/".$this->uploadBanner($request->file('banner')));

        $post = $this->post->create($attributes);

        session()->flash('status', [
            'type' => 'success',
            'message' =>'پست با موفقیت اضافه شد.'
        ]);
        return redirect()->route('posts.show',$post);

    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @ret'urn View
     * @return View
     */
    public function show(Post $post)
    {
        $current_page = request()->get('page') ?: "1";

        // Get Previous Post
        $previous = Post::where('id', '<', $post->id)->latest()->first() ?: Post::latest()->first();

        // Get Next Post
        $next = Post::where('id', '>', $post->id)->first() ?: Post::first();

        $post = Cache::rememberForever('post-'.$post->id,function () use($post){
            return $post->load('author','categories','tags');
        });


        $comments = Cache::tags('post-'.$post->id.'-comments')->rememberForever('post-'.$post->id.'-comments-'.$current_page,function () use($post){
            return $post->comments()->paginate(3);
        });
        if ($post->categories->count()>1){
            $category = $post->categories()->inRandomOrder()->first();
            $related_posts = $category->posts()->where('id','!=',$post->id)->get()->take(3);
        }
        else if($post->categories->count()==1){
            $category = $post->categories->first();
            $related_posts = $category->posts()->where('id','!=',$post->id)->get()->take(3);
        }
        else{
            $related_posts = [];
        }
        return view('blog.show',compact('post','comments','related_posts','previous','next'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Post $post
     * @return View
     * @throws AuthorizationException
     */
    public function edit(Post $post)
    {
        $this->authorize('update',$post);

        return view('panel.posts.edit',compact('post'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param PostRequest $request
     * @param Post $post
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(PostRequest $request, Post $post)
    {
        $this->authorize('update',$post);
        $attributes = $request->validated();
        if ($request->has('banner')){
            Storage::delete("images/banners/".basename($post->banner));
            $attributes['banner'] = url("/images/banners/".$this->uploadBanner($request->file('banner')));
        }
        $this->post->update($post,$attributes);
        if ($post->is_published){
            PostDeleted::dispatch($post);
        }


        session()->flash('status', [
            'type' => 'success',
            'message' => 'پست با موفقیت آپدیت شد.'
        ]);
        return redirect()->route('posts.show',$post);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete',$post);
        if ($post->is_published){
            PostDeleted::dispatch($post);
        }
        Storage::delete("images/banners/".basename($post->banner));

        $this->post->delete($post);
        session()->flash('status', [
            'type' => 'success',
            'message' =>'پست با موفقیت حذف شد.'
        ]);

        return back();
    }
}
