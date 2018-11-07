<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\Response as HttpResponse;

class UserController extends Controller
{
    //

    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }



    public function store(Request $request)
    {
        if ($errors = $this->model->handleValidatation()) {
            return $this->respondValidationError($errors);
        }

        return $this
            ->statusCode(HttpResponse::HTTP_CREATED)
            ->respond($this->model->create($request->all()));
    }


    public function users(){
        $tags= User::all();
        if ($tags){
            return $this->respond($tags);
        }else{
            return $this->respondNotFound($tags);
        }
    }

}
