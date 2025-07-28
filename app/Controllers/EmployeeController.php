<?php
namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\Common;
use Exception;

class EmployeeController extends ResourceController
{
    use ResponseTrait;
    protected $model;

    public function __construct()
    {
        $this->model = new Common();
    }

    public function getEmployees()
    {
        try {
            $data = $this->model->getRecords("employees");
            return $this->respond(["status" => 200, "data" => $data]);
        } catch (Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function addEmployee()
    {
        try {
            $json = $this->request->getJSON(true);

            $requiredFields = ['name', 'age', 'skills', 'address', 'designation'];

            foreach ($requiredFields as $field) {
                if (empty($json[$field])) {
                    return $this->failValidationError(ucfirst($field) . ' is required.');
                }
            }

            $employeeData = [
                'name' => $json['name'],
                'age' => $json['age'],
                'skills' => $json['skills'],
                'address' => $json['address'],
                'designation' => $json['designation'],
            ];

            $this->model->insertData("employees", $employeeData);

            return $this->respondCreated([
                'message' => 'Employee added successfully',
                'data' => $employeeData
            ]);
        } catch (Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }


    public function deleteEmployee()
    {
        try {
        } catch (Exception $e) {
        }

    }

    public function updateEmployee()
    {
        try {
        } catch (Exception $e) {
        }
    }

}
