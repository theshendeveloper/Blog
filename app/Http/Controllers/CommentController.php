<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Repositories\CommentRepository;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CommentController extends Controller
{

    /**
     * @var CommentRepository
     */
    private $comment;

    public function __construct(CommentRepository $comment)
    {
        $this->middleware('can:Delete-Comment')->only('destroy');
        $this->comment = $comment;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $current_page = request()->get('page') ?: "1";
        $comments =Cache::tags('comments')->rememberForever('comment-'.$current_page,function () {
            return $this->comment->getWith('post',5);
        });
        return view('panel.comments.index',compact('comments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @return RedirectResponse
     */
    public function store(Request $request,Post $post)
    {
        $rules =[
            'author' => 'required',
            'phone' => ['required','regex:/{?(0?9[0-9]{9,9}}?)$/'],
            'content' => 'required|string',
            'avatar' => 'required|string',
            'captcha' => 'required|captcha'
        ];
        if (app()->environment()=='testing'){
            unset($rules['captcha']);
        }
        $data = $request->validate($rules);
        unset($data['captcha']);
        $post->comments()->create($data);
        session()->flash('status', [
            'type' => 'success',
            'message' => 'نظر با موفقیت ارسال شد و پس از تایید، منتشر می شود.'
        ]);
        return redirect()->route('posts.show',$post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Comment $comment
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Comment $comment)
    {
        $this->comment->delete($comment);
        session()->flash('status', [
            'type' => 'success',
            'message' => 'کامنت با موفقیت حذف شد.'
        ]);
        return back();
    }
}
