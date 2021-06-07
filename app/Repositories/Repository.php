<?php


namespace App\Repositories;


use App\Models\Post;

abstract class Repository {
    protected $model;

    abstract public function model();

    public function __construct()
    {
        $this->model = app($this->model());
    }
    public function all()
    {
        return $this->model->latest()->get();
    }

    public function paginate($limit = 15)
    {
        return $this->model->latest()->paginate($limit);
    }

    public function getBy($col, $value, $limit = 15)
    {
        return $this->model->where($col, $value)->limit($limit)->get();
    }

    public function getWith($relation,$limit=5)
    {
        return $this->model->with($relation)->withoutGlobalScopes()->latest()->paginate($limit);

    }

    public function create(array $data) {
        return $this->model->create($data);
    }

    public function update($model,array $data) {
        return $model->update($data);
    }
    public function delete($model)
    {
        $model->delete();
    }

}
