<?php

use App\Models\Common;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{

    use ResponseTrait;
    protected $model;
    public function __construct()
    {
        $this->model = new Common();
    }
    public function login()
    {
        
    }

    public function logout(){
       
    }
}

?>