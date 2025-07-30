<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .login-container {
      max-width: 400px;
      margin: 80px auto;
      padding: 30px;
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .message {
      margin-top: 15px;
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="login-container">
      <h3 class="text-center mb-4">Login</h3>
      <form id="loginForm">
        <div class="mb-3">
          <input type="email" id="email" class="form-control" placeholder="Email" required />
        </div>
        <div class="mb-3">
          <input type="password" id="password" class="form-control" placeholder="Password" required />
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
      <div id="message" class="message text-center mt-3"></div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const form = document.getElementById('loginForm');
    const messageDiv = document.getElementById('message');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      messageDiv.textContent = '';
      messageDiv.className = 'message text-center mt-3';

      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;

      if (!email || !password) {
        messageDiv.textContent = 'Please fill in all fields.';
        messageDiv.classList.add('text-danger');
        return;
      }

      try {
        const response = await fetch('/api/auth/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ email, password })
        });

        const result = await response.json();

        if (response.ok) {
          messageDiv.textContent = result.message || 'Login successful!';
          messageDiv.classList.add('text-success');
          window.location.href = '/dashboard';
        } else {
          messageDiv.textContent = result.message || 'Login failed';
          messageDiv.classList.add('text-danger');
        }
      } catch (error) {
        messageDiv.textContent = 'An error occurred. Please try again.';
        messageDiv.classList.add('text-danger');
        console.error('Login error:', error);
      }
    });
  </script>

</body>

</html>