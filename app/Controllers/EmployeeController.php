<?php
namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\Common;
use Exception;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

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
            $request = $this->request;
            $draw = $request->getGet('draw');
            $start = $request->getGet('start') ?? 0;
            $length = $request->getGet('length') ?? 10;
            $limit = $request->getGet('limit') ?? $length;
            $search = $request->getGet('search')['value'] ?? '';
            $order = $request->getGet('order')[0] ?? ['column' => 0, 'dir' => 'asc'];

            // Columns must match your frontend DataTable order
            $columns = ['id', 'name', 'age', 'skills', 'address', 'designation'];
            $orderBy = $columns[$order['column']] ?? 'id';
            $orderDir = $order['dir'] ?? 'asc';

            // Columns allowed for search
            $searchableColumns = ['name', 'skills', 'address', 'designation'];

            $common = new \App\Models\Common();

            if ((int) $limit === -1) {
                // Export request: return all filtered data
                $data = $common->getPaginatedRecords('employees', -1, 0, $searchableColumns, $search, $orderBy, $orderDir);
                return $this->respond([
                    'status' => 200,
                    'data' => $data
                ]);
            }

            // Get paginated data for DataTable
            $data = $common->getPaginatedRecords('employees', $length, $start, $searchableColumns, $search, $orderBy, $orderDir);
            $recordsTotal = $common->countAllRows('employees');
            $recordsFiltered = $common->countFiltered('employees', $searchableColumns, $search);

            return $this->respond([
                'draw' => intval($draw),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
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
                'name' => $json['name'],
                'age' => $json['age'],
                'skills' => $json['skills'],
                'address' => $json['address'],
                'designation' => $json['designation'],
            ];

            $empId = $json['id'];

            $this->model->updateData("employees", ['id' => $empId], $updateData);

            return $this->respond([
                'status' => 200,
                'message' => 'Employee updated successfully',
                'data' => array_merge(['id' => $empId], $updateData)
            ]);
        } catch (Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    // ====== Export Employees to XLS ======
    public function exportEmployees()
    {
        try {
            log_message('debug', 'exportEmployees called');

            $json = $this->request->getJSON(true);
            log_message('debug', 'Input JSON: ' . json_encode($json));

            $which = $json['which'] ?? 'all';
            $format = strtolower($json['format'] ?? 'xls');
            $data = $json['data'] ?? null;

            if ($which === 'current' && is_array($data)) {
                $employees = $data;
            } else {
                $employees = $this->model->getRecords("employees");
            }

            if (empty($employees)) {
                log_message('debug', 'No employees found to export.');
                return $this->failNotFound('No employees found to export.');
            }

            log_message('debug', 'Exporting ' . count($employees) . ' employees as ' . $format);

            if ($format === 'pdf') {
                // PDF export (your existing PDF code)
                $html = '<h3>Employee List</h3>';
                $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
                $html .= '<thead><tr>
                        <th>ID</th><th>Name</th><th>Age</th><th>Skills</th><th>Address</th><th>Designation</th>
                      </tr></thead><tbody>';

                foreach ($employees as $emp) {
                    $html .= '<tr>
                            <td>' . htmlspecialchars($emp['id']) . '</td>
                            <td>' . htmlspecialchars($emp['name']) . '</td>
                            <td>' . htmlspecialchars($emp['age']) . '</td>
                            <td>' . htmlspecialchars($emp['skills']) . '</td>
                            <td>' . htmlspecialchars($emp['address']) . '</td>
                            <td>' . htmlspecialchars($emp['designation']) . '</td>
                          </tr>';
                }
                $html .= '</tbody></table>';

                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $pdfContent = $dompdf->output();

                return $this->response->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'attachment; filename="employees_' . date('Ymd_His') . '.pdf"')
                    ->setBody($pdfContent);

            } else {
                // XLSX export
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Header
                $sheet->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'Name')
                    ->setCellValue('C1', 'Age')
                    ->setCellValue('D1', 'Skills')
                    ->setCellValue('E1', 'Address')
                    ->setCellValue('F1', 'Designation');

                $row = 2;
                foreach ($employees as $emp) {
                    $sheet->setCellValue('A' . $row, $emp['id']);
                    $sheet->setCellValue('B' . $row, $emp['name']);
                    $sheet->setCellValue('C' . $row, $emp['age']);
                    $sheet->setCellValue('D' . $row, $emp['skills']);
                    $sheet->setCellValue('E' . $row, $emp['address']);
                    $sheet->setCellValue('F' . $row, $emp['designation']);
                    $row++;
                }

                $writer = new Xlsx($spreadsheet);

                // Save to a temp file in writable/temp folder (create folder if not exists)
                $tempPath = WRITEPATH . 'temp/';
                if (!is_dir($tempPath)) {
                    mkdir($tempPath, 0755, true);
                }

                $temp_file = tempnam($tempPath, 'xls');
                $writer->save($temp_file);

                // Return file as download response
                return $this->response->download('employees_' . date('Ymd_His') . '.xlsx', file_get_contents($temp_file), true)
                    ->setFileName('employees_' . date('Ymd_His') . '.xlsx')
                    ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

                // Clean up temp file
                unlink($temp_file);
            }
        } catch (Exception $e) {
            log_message('error', 'Export failed: ' . $e->getMessage());
            return $this->failServerError($e->getMessage());
        }
    }

}
