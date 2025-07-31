<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Employee Records</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- XLSX Export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

  <!-- jsPDF for PDF export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
  <style>
    #loaderOverlay {
      display: none !important;
    }
  </style>
</head>

<body class="bg-light">

  <!-- Export Modal -->
  <div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Export Data</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="font-weight-bold">Export Scope</label><br>
            <div class="form-check">
              <input type="radio" name="whichExport" value="all" class="form-check-input" checked>
              <label class="form-check-label">All Records</label>
            </div>
            <div class="form-check">
              <input type="radio" name="whichExport" value="current" class="form-check-input">
              <label class="form-check-label">Displayed Records</label>
            </div>
          </div>
          <div class="form-group">
            <label class="font-weight-bold">Select Format</label>
            <select class="form-control" id="exportFormat">
              <option value="xls">Excel (.xls)</option>
              <option value="pdf">PDF (.pdf)</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button id="startExportBtn" type="button" class="btn btn-primary">Export</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Page Container -->
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="text-dark">Employee Records</h2>
      <button class="btn btn-outline-danger" id="logoutBtn">Logout</button>
    </div>

    <!-- Alert Box -->
    <div id="alertBox" class="alert d-none" role="alert"></div>

    <!-- Actions -->
    <div class="mb-3 d-flex flex-wrap align-items-center">
      <button class="btn btn-success mr-2" id="addNewBtn">
        <i class="fas fa-plus"></i> Add New Record
      </button>
      <button class="btn btn-info" data-toggle="modal" data-target="#exportModal">
        <i class="fas fa-download"></i> Download Data
      </button>
    </div>

    <!-- Table -->
    <div class="table-responsive bg-white shadow rounded p-3">
      <table id="employeeTable" class="table table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Age</th>
            <th>Skills</th>
            <th>Address</th>
            <th>Designation</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- Employee Form Modal -->
  <div class="modal fade" id="empModal" tabindex="-1">
    <div class="modal-dialog">
      <form id="empForm" class="w-100">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Employee Form</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="empId">
            <div class="form-group">
              <label>Name</label>
              <input type="text" class="form-control" id="name">
              <div class="invalid-feedback" id="nameError"></div>
            </div>

            <div class="form-group">
              <label>Age</label>
              <input type="number" class="form-control" id="age">
              <div class="invalid-feedback" id="ageError"></div>
            </div>

            <div class="form-group">
              <label>Skills</label>
              <input type="text" class="form-control" id="skills">
              <div class="invalid-feedback" id="skillsError"></div>
            </div>

            <div class="form-group">
              <label>Address</label>
              <input type="text" class="form-control" id="address">
              <div class="invalid-feedback" id="addressError"></div>
            </div>

            <div class="form-group">
              <label>Designation</label>
              <input type="text" class="form-control" id="designation">
              <div class="invalid-feedback" id="designationError"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-sm">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Confirm Deletion</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this record?</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </div>
  </div>

  <div id="loaderOverlay" class="d-flex" style="
    display: none !important;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(255, 255, 255, 0.7);
    z-index: 9999;
    align-items: center;
    justify-content: center;
  ">
    <div class="spinner-border text-primary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>

<script type="module" src="/dashboard-script.js"></script>

</html>