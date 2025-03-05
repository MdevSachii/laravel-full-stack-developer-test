<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>

    <h1>Login</h1>

    <form id="login-form">
        <input type="email" id="email" placeholder="Email" required>
        <input type="password" id="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <script>
        document.getElementById('login-form').addEventListener('submit', function (event) {
            event.preventDefault();
            
            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;

            axios.post('{{route('api.login')}}', { email, password })
            .then(response => {
                localStorage.setItem('token', response.data.token); 
                window.location.href = '/packages'; 
            })
            .catch(error => {
                alert('Login failed');
            });
        });
    </script>

</body>
</html>
