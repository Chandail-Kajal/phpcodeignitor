<!DOCTYPE html>
<html lang="en">
<head>
  <title>Employee Data Entry</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link
    rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
  />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
  <div class="container">
    <h2>Data Entry</h2>
    <form id="employeeForm" novalidate>
      <div class="form-group">
        <label for="name">Name:<span style="color:red">*</span></label>
        <input
          type="text"
          class="form-control"
          id="name"
          placeholder="Enter your name"
          name="name"
          required
        />
      </div>
      <div class="form-group">
        <label for="age">Age:<span style="color:red">*</span></label>
        <input
          type="number"
          class="form-control"
          id="age"
          placeholder="Enter your age"
          name="age"
          required
          min="1"
        />
      </div>
      <div class="form-group">
        <label for="skills">Skills:<span style="color:red">*</span></label>
        <input
          type="text"
          class="form-control"
          id="skills"
          placeholder="Enter your skills"
          name="skills"
          required
        />
      </div>
      <div class="form-group">
        <label for="address">Address:<span style="color:red">*</span></label>
        <input
          type="text"
          class="form-control"
          id="address"
          placeholder="Enter your permanent address"
          name="address"
          required
        />
      </div>
      <div class="form-group">
        <label for="designation">Designation:<span style="color:red">*</span></label>
        <input
          type="text"
          class="form-control"
          id="designation"
          placeholder="Enter your designation"
          name="designation"
          required
        />
      </div>

      <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <div id="message" style="margin-top: 15px;"></div>
  </div>

  <script>
    document.getElementById('employeeForm').addEventListener('submit', async function (e) {
      e.preventDefault();

      const messageDiv = document.getElementById('message');
      messageDiv.innerHTML = '';

     
      const name = this.name.value.trim();
      const age = this.age.value.trim();
      const skills = this.skills.value.trim();
      const address = this.address.value.trim();
      const designation = this.designation.value.trim();

      if (!name) {
        messageDiv.innerHTML = '<div class="alert alert-danger">Name is required.</div>';
        return;
      }
      if (!age || isNaN(age) || age <= 0) {
        messageDiv.innerHTML = '<div class="alert alert-danger">Please enter a valid positive age.</div>';
        return;
      }
      if (!skills) {
        messageDiv.innerHTML = '<div class="alert alert-danger">Skills are required.</div>';
        return;
      }
      if (!address) {
        messageDiv.innerHTML = '<div class="alert alert-danger">Address is required.</div>';
        return;
      }
      if (!designation) {
        messageDiv.innerHTML = '<div class="alert alert-danger">Designation is required.</div>';
        return;
      }

     
      const data = { name, age: Number(age), skills, address, designation };

      try {
        const response = await fetch('/emp', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data),
        });

        const result = await response.json();

        if (response.ok) {
          messageDiv.innerHTML = `<div class="alert alert-success">${result.message || 'Employee added successfully.'}</div>`;
          this.reset(); 
        } else {
          const errMsg = result.messages?.error || result.message || 'Failed to add employee.';
          messageDiv.innerHTML = `<div class="alert alert-danger">${errMsg}</div>`;
        }
      } catch (error) {
        messageDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
      }
    });
  </script>
</body>
</html>

