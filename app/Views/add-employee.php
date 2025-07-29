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
    <h2>Data Entry</h2>
    <form id="employeeForm" novalidate>
      <div class="form-group">
        <label for="name">Name:<span style="color:red">*</span></label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required />
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
        <input type="text" class="form-control" id="address" name="address" placeholder="Enter your permanent address" required />
      </div>
      <div class="form-group">
        <label for="designation">Designation:<span style="color:red">*</span></label>
        <input type="text" class="form-control" id="designation" name="designation" placeholder="Enter your designation" required />
      </div>

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
    event.target.reset();
  } else {
    const errMsg = result?.error || result?.message || 'Submission failed.';
    messageDiv.innerHTML = `<div class="alert alert-danger">${errMsg}</div>`;
  }
} catch (error) {
  messageDiv.innerHTML = `<div class="alert alert-danger">Network or server error: ${error.message}</div>`;
}

 finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit';
      }
    });
  </script>
</body>
</html>
