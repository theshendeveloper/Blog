<?php


namespace App\Repositories;


use App\Models\Comment;

class CommentRepository extends Repository {

    public function model()
    {
        return Comment::class;
    }
}
