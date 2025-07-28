<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Employee Records</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
</head>

<body>

  <div class="container mt-4">
    <h2>Employee Records</h2>
    <div id="alertBox" class="alert d-none" role="alert"></div>

    <div class="d-flex justify-content-between mb-3">
      <button class="btn btn-success" id="addNewBtn">Add New Record</button>
      <button class="btn btn-danger" id="logoutBtn">Logout</button>
    </div>

    <table id="employeeTable" class="display table table-bordered">
      <thead>
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

  <!-- Modal for Add/Edit -->
  <div class="modal fade" id="empModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <form id="empForm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Employee Form</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="empId">
            <div class="form-group">
              <label>Name</label>
              <input type="text" class="form-control" id="name" required>
            </div>
            <div class="form-group">
              <label>Age</label>
              <input type="number" class="form-control" id="age" required>
            </div>
            <div class="form-group">
              <label>Skills</label>
              <input type="text" class="form-control" id="skills" required>
            </div>
            <div class="form-group">
              <label>Address</label>
              <input type="text" class="form-control" id="address" required>
            </div>
            <div class="form-group">
              <label>Designation</label>
              <input type="text" class="form-control" id="designation" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script>
    let table;
    let employeeData = [];

    function showAlert(message, type = 'success') {
      const alertBox = document.getElementById('alertBox');
      alertBox.textContent = message;
      alertBox.className = `alert alert-${type}`;
      alertBox.classList.remove('d-none');

      setTimeout(() => {
        alertBox.classList.add('d-none');
      }, 3000);
    }


    document.getElementById("logoutBtn").addEventListener("click", async () => {
      const res = await fetch("/auth/logout")
      if (res.ok) {
        alert("Logged out successfully")
        window.location.href = "/login"
      } else {
        alert("Logout failed")
      }
    })

    async function loadTable() {
      const response = await fetch('/emp');
      const json = await response.json();
      employeeData = json.data || [];

      if (table) {
        table.clear().rows.add(employeeData).draw();
      } else {
        table = $('#employeeTable').DataTable({
          data: employeeData,
          columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'age' },
            { data: 'skills' },
            { data: 'address' },
            { data: 'designation' },
            {
              data: null,
              render: (data, type, row) => `
                <button class="btn btn-warning btn-sm editBtn" data-id="${row.id}">Update</button>
                <button class="btn btn-danger btn-sm deleteBtn" data-id="${row.id}">Delete</button>
              `
            }
          ]
        });
      }
    }

    function fuzzyMatch(value, keyword) {
      return value.toLowerCase().includes(keyword.toLowerCase());
    }

    function filterAndRenderTable(keyword) {
      const filtered = employeeData.filter(emp =>
        Object.values(emp).some(val =>
          fuzzyMatch(String(val), keyword)
        )
      );

      table.clear().rows.add(filtered).draw();
    }

    document.addEventListener('DOMContentLoaded', () => {
      loadTable();

      document.getElementById('addNewBtn').addEventListener('click', () => {
        document.getElementById('empForm').reset();
        document.getElementById('empId').value = '';
        $('#empModal').modal('show');
      });

      document.getElementById('empForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('empId').value;
        const empData = {
          name: document.getElementById('name').value,
          age: document.getElementById('age').value,
          skills: document.getElementById('skills').value,
          address: document.getElementById('address').value,
          designation: document.getElementById('designation').value
        };

        if (id) empData.id = id;

        const res = await fetch('/emp', {
          method: id ? 'PUT' : 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(empData)
        });

        if (res.ok) {
          showAlert(id ? 'Employee updated successfully!' : 'Employee added successfully!', 'success');
        } else {
          showAlert('Operation failed. Please try again.', 'danger');
        }

        $('#empModal').modal('hide');
        await loadTable();
      });

      // Event delegation for edit/delete
      document.querySelector('#employeeTable tbody').addEventListener('click', async function (e) {
        const target = e.target;

        if (target.classList.contains('editBtn')) {
          const id = parseInt(target.getAttribute('data-id'));
          console.log(id)
          const employee = employeeData.find(emp => emp.id == id);
          if (!employee) return alert('Employee not found.');

          document.getElementById('empId').value = employee.id;
          document.getElementById('name').value = employee.name;
          document.getElementById('age').value = employee.age;
          document.getElementById('skills').value = employee.skills;
          document.getElementById('address').value = employee.address;
          document.getElementById('designation').value = employee.designation;
          $('#empModal').modal('show');
        }

        if (target.classList.contains('deleteBtn')) {
          const id = parseInt(target.getAttribute('data-id'));
          if (confirm('Are you sure you want to delete this record?')) {
            const res = await fetch('/emp', {
              method: 'DELETE',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ id })
            });

            if (res.ok) {
              await loadTable();
              showAlert('Record deleted successfully!', 'success');
            } else {
              showAlert('Failed to delete record.', 'danger');
            }
          }
        }
      });
    });
  </script>

</body>

</html>