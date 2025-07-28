<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 50px; }
    input, button { padding: 10px; margin: 5px 0; width: 100%; }
    .message { margin-top: 15px; padding: 10px; }
    .success { color: green; }
    .error { color: red; }
  </style>
</head>
<body>

  <h2>Login</h2>
  <form id="loginForm">
    <input type="email" id="email" placeholder="Email" required />
    <input type="password" id="password" placeholder="Password" required />
    <button type="submit">Login</button>
  </form>

  <div id="message" class="message"></div>

  <script>
    const form = document.getElementById('loginForm');
    const messageDiv = document.getElementById('message');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      messageDiv.textContent = '';
      messageDiv.className = 'message';

      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;

      if (!email || !password) {
        messageDiv.textContent = 'Please fill in all fields.';
        messageDiv.classList.add('error');
        return;
      }

      try {
        const response = await fetch('/auth/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ email, password })
        });

        const result = await response.json();

        if (response.ok) {
          messageDiv.textContent = result.message || 'Login successful!';
          messageDiv.classList.add('success');
          // Optional: redirect
          // window.location.href = '/dashboard';
        } else {
          messageDiv.textContent = result.message || 'Login failed';
          messageDiv.classList.add('error');
        }
      } catch (error) {
        messageDiv.textContent = 'An error occurred. Please try again.';
        messageDiv.classList.add('error');
        console.error('Login error:', error);
      }
    });
  </script>

</body>
</html>
