<?php

namespace App\Http\Helper;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Filters
{
    private $model, $request;

    public function __construct(Model | Builder $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    public function search(string $column)
    {
        if ($this->request->search) {
            $this->model = $this->model->where($column, "like", "%{$this->request->search}%");
        }

        return $this;
    }

    public function after()
    {
        if ($this->request->after) {
            $this->model = $this->model->where("created_at", ">", $this->request->after);
        }

        return $this;
    }

    public function before()
    {
        if ($this->request->before) {
            $this->model = $this->model->where("created_at", "<", $this->request->before);
        }

        return $this;
    }

    public function result($take = 100): Builder
    {
        if ($this->request->take) {
            return $this->model->take($this->request->take);
        }

        return $this->model->take($take);
    }
}
