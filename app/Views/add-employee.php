<!DOCTYPE html>
<html lang="en">

<head>
  <title>Employee Data Entry</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>
  <div class="container">
    <h2>Employee Data Entry</h2>

    <form id="employeeForm" novalidate>

      <div class="form-group">
        <label for="name">Name:<span style="color:red">*</span></label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required />
        <div id="nameError" class="text-danger"></div>

      </div>
      <div class="form-group">
        <label for="age">Age:<span style="color:red">*</span></label>
        <input type="number" class="form-control" id="age" name="age" placeholder="Enter your age" required min="1" />
      </div>
      <div class="form-group">
        <label for="skills">Skills:<span style="color:red">*</span></label>
        <input type="text" class="form-control" id="skills" name="skills" placeholder="Enter your skills" required />
      </div>
      <div class="form-group">
        <label for="address">Address:<span style="color:red">*</span></label>
        <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address" required />
      </div>
      <div class="form-group">
        <label for="designation">Designation:<span style="color:red">*</span></label>
        <input type="text" class="form-control" id="designation" name="designation" placeholder="Enter your designation"
          required />
      </div>
      <script>
        // Real-time input validation with empty check
        function validateField(field, regex, message) {
          const errorId = `${field.id}Error`;
          let errorElem = document.getElementById(errorId);

          if (!errorElem) {
            errorElem = document.createElement('div');
            errorElem.id = errorId;
            errorElem.className = 'text-danger';
            field.parentElement.appendChild(errorElem);
          }

          const value = field.value.trim();

          if (value === '') {
            // Hide error if empty (no input)
            errorElem.textContent = '';
            return false;
          }

          if (!regex.test(value)) {
            errorElem.textContent = message;
            return false;
          } else {
            errorElem.textContent = '';
            return true;
          }
        }

        document.addEventListener('DOMContentLoaded', () => {
          const nameField = document.getElementById('name');
          const ageField = document.getElementById('age');
          const designationField = document.getElementById('designation');
          const addressField = document.getElementById('address');
          const skillsField = document.getElementById('skills');

          nameField.addEventListener('input', () => {
            validateField(nameField, /^[a-zA-Z\s]+$/, 'Name must contain only letters and spaces.');
          });

          ageField.addEventListener('input', () => {
            validateField(ageField, /^(1[8-9]|[2-4][0-9]|5[0-5])$/, 'Age must be a number between 18 and 55.');
          });

          designationField.addEventListener('input', () => {
            validateField(designationField, /^[a-zA-Z\s]+$/, 'Designation must contain only letters and spaces.');
          });

          addressField.addEventListener('input', () => {
            validateField(addressField, /^[a-zA-Z0-9\s,.\-]+$/, 'Address must be valid (letters, numbers, , . -).');
          });

          skillsField.addEventListener('input', () => {
            validateField(skillsField, /^[a-zA-Z\s,.\-]+$/, 'Skills must contain only letters, spaces, commas, dots, or hyphens.');
          });
        });
      </script>



      <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
    </form>

    <div id="message" style="margin-top: 15px;"></div>
  </div>

  <script>
    document.getElementById('employeeForm').addEventListener('submit', async function (e) {
      e.preventDefault();

      const messageDiv = document.getElementById('message');
      const submitBtn = document.getElementById('submitBtn');
      messageDiv.innerHTML = '';
      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting...';

      const name = this.name.value.trim();
      const age = this.age.value.trim();
      const skills = this.skills.value.trim();
      const address = this.address.value.trim();
      const designation = this.designation.value.trim();

      if (!name || !age || isNaN(age) || age <= 0 || age > 100 || !skills || !address || !designation) {
        messageDiv.innerHTML = '<div class="alert alert-danger">Please fill all fields correctly.</div>';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit';
        return;
      }

      const data = {
        name,
        age: Number(age),
        skills,
        address,
        designation
      };

     try {
  const response = await fetch('/emp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  });

  const contentType = response.headers.get('Content-Type') || '';
  let result = {};

  if (contentType.includes('application/json')) {
    result = await response.json();
  } else {
    const text = await response.text();
    throw new Error(`Server returned non-JSON response: ${text.slice(0, 100)}...`);
  }

  if (response.ok) {
    messageDiv.innerHTML = `<div class="alert alert-success">${result.message || 'Employee added successfully.'}</div>`;
    this.reset();

    // Clear all previous field error messages
    ['name', 'age', 'skills', 'address', 'designation'].forEach(field => {
      const errorDiv = document.getElementById(`${field}Error`);
      if (errorDiv) errorDiv.textContent = '';
    });

  } else if (response.status === 400 && result.errors) {
    // Show server-side validation errors next to fields
    Object.keys(result.errors).forEach(field => {
      const errorDiv = document.getElementById(`${field}Error`);
      if (errorDiv) {
        errorDiv.textContent = result.errors[field];
      }
    });
    messageDiv.innerHTML = `<div class="alert alert-danger">Please fix the highlighted errors.</div>`;
  } else {
    const errMsg = result?.error || result?.message || 'Submission failed.';
    messageDiv.innerHTML = `<div class="alert alert-danger">${errMsg}</div>`;
  }

} catch (error) {
  messageDiv.innerHTML = `<div class="alert alert-danger">Network or server error: ${error.message}</div>`;
}

    });
  </script>
</body>

</html>