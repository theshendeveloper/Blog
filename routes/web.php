<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/','BlogController@index')->name('blog.index');

Route::group(['prefix' => 'adwise_panel'],function (){
    Auth::routes(['register'=>false]);

    Route::middleware(['auth'])->group(function (){
        Route::get('dashboard','PanelController@dashboard')->name('dashboard');
        Route::resource('categories','CategoryController')->except(['create','show'])->middleware('can:Write-Post');
        Route::resource('posts','PostController')->except('show');
        Route::post('publish-post/{post}','PublishPostController@store')->middleware('can:Publish-Post')->name('post.publish.store');
        Route::delete('publish-post/{post}','PublishPostController@destroy')->middleware('can:Unpublish-Post')->name('post.publish.destroy');
        Route::resource('comments','CommentController')->only(['index','destroy'])->middleware('can:Publish-Comment');
        Route::post('publish-comment/{comment}','PublishCommentController@store')->middleware('can:Publish-Comment')->name('comment.publish.store');
        Route::delete('publish-comment/{comment}','PublishCommentController@destroy')->middleware('can:Unpublish-Comment')->name('comment.publish.destroy');
    });


});
Route::group(['prefix'=>'blog'], function (){
    Route::get('posts/{post}','PostController@show')->name('posts.show');
    Route::post('post-editor/upload-image','EditorImageUploadController')->name('editor.upload');
    Route::get('categories/{category}/posts','CategoryPostsController')->name('category.posts.index');
    Route::get('tags/{tag}/posts','TagPostsController')->name('tag.posts.index');
    Route::post('posts/{post}/comments','CommentController@store')->name('comments.store');
    Route::post('subscribe','SubscriberController')->name('subscribe.store');

});
Route::get('refreshcaptcha', 'CaptchaController@refreshCaptcha');
