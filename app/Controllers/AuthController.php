<?php

namespace App\Controllers;

use App\Models\Common;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $session;

    public function __construct()
    {
        $this->model = new Common();
        $this->session = session();
    }

    public function login()
    {
        $data = $this->request->getJSON(true); 

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (! $this->validateData($data, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $email    = $data['email'];
        $password = $data['password'];

        $user = $this->model->getRecord('users', ['email' => $email]);

        if (! $user) {
            return $this->failNotFound('Email not found');
        }

        if (! password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Invalid password');
        }

        $this->session->set([
            'user_id'   => $user['id'],
            'email'     => $user['email'],
            'logged_in' => true
        ]);

        return $this->respond([
            'status'  => 200,
            'message' => 'Login successful',
            'data'    => [
                'user_id' => $user['id'],
                'email'   => $user['email']
            ]
        ]);
    }

    public function logout()
    {
        $this->session->destroy();

        return $this->respond([
            'status'  => 200,
            'message' => 'Logged out successfully'
        ]);
    }
}

?>
