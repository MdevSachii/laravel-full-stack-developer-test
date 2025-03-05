<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>

    <h1>Register</h1>

    <form id="register-form">
        <input type="text" id="name" placeholder="Full Name" required>
        <input type="email" id="email" placeholder="Email" required>
        <input type="password" id="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <script>
        document.getElementById('register-form').addEventListener('submit', function (event) {
            event.preventDefault();

            let name = document.getElementById('name').value;
            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;

            axios.post('{{route('api.register')}}', { name, email, password })
            .then(response => {
                alert('Registration successful!');
                localStorage.setItem('token', response.data.token); 
                window.location.href = '/packages'; 
            })
            .catch(error => {
                alert('Registration failed');
            });
        });
    </script>

</body>
</html>
