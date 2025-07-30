<!DOCTYPE html>
<html lang="en">

<head>
  <title>Employee Data Entry</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  <style>
    body {
      background: #f9f9f9;
      padding-top: 40px;
    }

    .container {
      max-width: 600px;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .form-group {
      position: relative;
    }

    .text-danger {
      position: absolute;
      bottom: -18px;
      left: 0;
      font-size: 12px;
    }

    #message {
      margin-top: 20px;
    }

    #dashboardBtn {
      margin-left: 10px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2 class="text-center">Employee Data Entry</h2>
    <form id="employeeForm" novalidate>
      <div class="form-group">
        <label for="name">Name:<span style="color:red">*</span></label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required />
        <div id="nameError" class="text-danger"></div>
      </div>

      <div class="form-group">
        <label for="age">Age:<span style="color:red">*</span></label>
        <input type="number" class="form-control" id="age" name="age" placeholder="Enter your age" required min="18" max="55" />
        <div id="ageError" class="text-danger"></div>
      </div>

      <div class="form-group">
        <label for="skills">Skills:<span style="color:red">*</span></label>
        <input type="text" class="form-control" id="skills" name="skills" placeholder="Enter your skills" required />
        <div id="skillsError" class="text-danger"></div>
      </div>

      <div class="form-group">
        <label for="address">Address:<span style="color:red">*</span></label>
        <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address" required />
        <div id="addressError" class="text-danger"></div>
      </div>

      <div class="form-group">
        <label for="designation">Designation:<span style="color:red">*</span></label>
        <input type="text" class="form-control" id="designation" name="designation" placeholder="Enter your designation" required />
        <div id="designationError" class="text-danger"></div>
      </div>

      <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
      <button type="button" id="dashboardBtn" class="btn btn-default">Go to Dashboard</button>
    </form>

    <div id="message"></div>
  </div>

  <script>
    // Real-time validation function
    function validateField(field, regex, message) {
      const errorId = `${field.id}Error`;
      let errorElem = document.getElementById(errorId);
      const value = field.value.trim();

      if (value === '') {
        errorElem.textContent = 'This field is required.';
        return false;
      }

      if (!regex.test(value)) {
        errorElem.textContent = message;
        return false;
      }

      errorElem.textContent = '';
      return true;
    }

    function validateAgeField(field) {
      const errorId = `${field.id}Error`;
      let errorElem = document.getElementById(errorId);
      const value = field.value.trim();

      if (value === '') {
        errorElem.textContent = 'This field is required.';
        return false;
      }

      const age = Number(value);

      if (isNaN(age) || age < 18 || age > 55) {
        errorElem.textContent = 'Age must be a number between 18 and 55.';
        return false;
      }

      errorElem.textContent = '';
      return true;
    }

    document.addEventListener('DOMContentLoaded', () => {
      const nameField = document.getElementById('name');
      const ageField = document.getElementById('age');
      const designationField = document.getElementById('designation');
      const addressField = document.getElementById('address');
      const skillsField = document.getElementById('skills');
      const dashboardBtn = document.getElementById('dashboardBtn');

      nameField.addEventListener('input', () => {
        validateField(nameField, /^[a-zA-Z\s]+$/, 'Name must contain only letters and spaces.');
      });

      ageField.addEventListener('input', () => {
        validateAgeField(ageField);
      });

      designationField.addEventListener('input', () => {
        validateField(designationField, /^[a-zA-Z\s]+$/, 'Designation must contain only letters and spaces.');
      });

      addressField.addEventListener('input', () => {
        validateField(addressField, /^[a-zA-Z0-9\s,.\-]+$/, 'Address must be valid (letters, numbers, commas, dots, hyphens).');
      });

      skillsField.addEventListener('input', () => {
        validateField(skillsField, /^[a-zA-Z\s,.\-]+$/, 'Skills must contain only letters, spaces, commas, dots, or hyphens.');
      });

      dashboardBtn.addEventListener('click', () => {
        window.location.href = '/dashboard';
      });

      // Form submit event
      document.getElementById('employeeForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const messageDiv = document.getElementById('message');
        const submitBtn = document.getElementById('submitBtn');
        messageDiv.innerHTML = '';
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        // Run all validations before submit
        const validName = validateField(nameField, /^[a-zA-Z\s]+$/, 'Name must contain only letters and spaces.');
        const validAge = validateAgeField(ageField);
        const validDesignation = validateField(designationField, /^[a-zA-Z\s]+$/, 'Designation must contain only letters and spaces.');
        const validAddress = validateField(addressField, /^[a-zA-Z0-9\s,.\-]+$/, 'Address must be valid (letters, numbers, commas, dots, hyphens).');
        const validSkills = validateField(skillsField, /^[a-zA-Z\s,.\-]+$/, 'Skills must contain only letters, spaces, commas, dots, or hyphens.');

        if (!validName || !validAge || !validDesignation || !validAddress || !validSkills) {
          messageDiv.innerHTML = '<div class="alert alert-danger">Please fix the errors before submitting.</div>';
          submitBtn.disabled = false;
          submitBtn.textContent = 'Submit';
          return;
        }

        const data = {
          name: nameField.value.trim(),
          age: Number(ageField.value.trim()),
          skills: skillsField.value.trim(),
          address: addressField.value.trim(),
          designation: designationField.value.trim()
        };

        try {
          const response = await fetch('/api/emp', {
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

            // Clear all previous error messages
            ['name', 'age', 'skills', 'address', 'designation'].forEach(field => {
              const errorDiv = document.getElementById(`${field}Error`);
              if (errorDiv) errorDiv.textContent = '';
            });
          } else if (response.status === 400 && result.errors) {
            Object.keys(result.errors).forEach(field => {
              const errorDiv = document.getElementById(`${field}Error`);
              if (errorDiv) errorDiv.textContent = result.errors[field];
            });
            messageDiv.innerHTML = `<div class="alert alert-danger">Please fix the highlighted errors.</div>`;
          } else {
            const errMsg = result?.error || result?.message || 'Submission failed.';
            messageDiv.innerHTML = `<div class="alert alert-danger">${errMsg}</div>`;
          }
        } catch (error) {
          messageDiv.innerHTML = `<div class="alert alert-danger">Network or server error: ${error.message}</div>`;
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Submit';
        }
      });
    });
  </script>
</body>

</html>
