<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Employee Records</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
</head>

<body>

  <div class="container mt-4">
    <h2 class="mb-3">Employee Records</h2>
    <button class="btn btn-success mb-3" id="addNewBtn">Add New Record</button>

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
  <div class="modal fade" id="empModal" tabindex="-1" role="dialog" aria-labelledby="empModalLabel">
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
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> <!-- Required by DataTables -->
  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script>
    let table;

    async function loadTable() {
      const response = await fetch('/emp');
      const json = await response.json();
      const { data } = json;

      if (table) {
        table.clear().rows.add(data).draw();
      } else {
        table = $('#employeeTable').DataTable({
          data: data,
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

    document.addEventListener('DOMContentLoaded', () => {
      loadTable();

      document.getElementById('addNewBtn').addEventListener('click', () => {
        document.getElementById('empForm').reset();
        document.getElementById('empId').value = '';
        $('#empModal').modal('show');
      });

      // Submit form - Create or Update
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

        const url = id ? `/emp/${id}` : '/emp';
        const method = id ? 'PUT' : 'POST';

        await fetch(url, {
          method,
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(empData)
        });

        $('#empModal').modal('hide');
        await loadTable();
      });

      // Event delegation for Edit button
      document.querySelector('#employeeTable tbody').addEventListener('click', async function (e) {
        if (e.target.classList.contains('editBtn')) {
          const id = e.target.getAttribute('data-id');
          const res = await fetch(`/emp/${id}`);
          const data = await res.json();

          document.getElementById('empId').value = data.id;
          document.getElementById('name').value = data.name;
          document.getElementById('age').value = data.age;
          document.getElementById('skills').value = data.skills;
          document.getElementById('address').value = data.address;
          document.getElementById('designation').value = data.designation;

          $('#empModal').modal('show');
        }

        // Delete button
        if (e.target.classList.contains('deleteBtn')) {
          const id = e.target.getAttribute('data-id');
          if (confirm('Are you sure you want to delete this record?')) {
            try {
              const response = await fetch('/emp', {
                method: 'DELETE',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
              });

              if (response.ok) {
                await loadTable();
              } else {
                const errorData = await response.json();
                alert(`Delete failed: ${errorData.message || 'Unknown error'}`);
              }
            } catch (err) {
              console.error('Error deleting employee:', err);
              alert('An unexpected error occurred.');
            }
          } 
          loadTable();
        }
      });
    });
  </script>

</body>

</html>