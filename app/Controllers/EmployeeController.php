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
    } catch (\Throwable $e) {
        log_message('error', $e->getMessage());

        return $this->respond([
            'status' => 500,
            'error' => true,
            'message' => 'An internal error occurred.'
        ], 500);
    }
}



    // public function deleteEmployee()
    // { 
    //     try {
    //         $json = $this->request->getJSON(true);
    //         $empId =$json['id'];
    //         $this->model->deleteData("employees",['id'=>$empId]);
    //     } catch (Exception $e) {
    //     }

    // }

    public function deleteEmployee()
{
    try {
        $json = $this->request->getJSON(true);

        if (empty($json['id'])) {
            return $this->failValidationError('ID is required for deletion.');
        }

        $empId = $json['id'];
        $deleted = $this->model->deleteData("employees", ['id' => $empId]);

        if ($deleted) {
            return $this->respondDeleted(['message' => 'Employee deleted successfully.']);
        } else {
            return $this->failNotFound('Employee not found or already deleted.');
        }
    } catch (Exception $e) {
        return $this->failServerError($e->getMessage());
    }
}

    public function updateEmployee()
    {
        try {
            $json = $this->request->getJSON(true);

            if (empty($json['id'])) {
                return $this->failValidationError('ID is required for update.');
            }

            $requiredFields = ['name', 'age', 'skills', 'address', 'designation'];

            foreach ($requiredFields as $field) {
                if (empty($json[$field])) {
                    return $this->failValidationError(ucfirst($field) . ' is required.');
                }
            }

            $updateData = [
                'name'        => $json['name'],
                'age'         => $json['age'],
                'skills'      => $json['skills'],
                'address'     => $json['address'],
                'designation' => $json['designation'],
            ];

            $empId = $json['id'];

            $this->model->updateData("employees", ['id' => $empId], $updateData);

            return $this->respond([
                'status'  => 200,
                'message' => 'Employee updated successfully',
                'data'    => array_merge(['id' => $empId], $updateData)
            ]);
        } catch (Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }


}
