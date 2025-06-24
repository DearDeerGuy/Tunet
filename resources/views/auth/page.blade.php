<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Auth Test Page</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: sans-serif; padding: 2em; max-width: 600px; margin: auto; }
        input, button { padding: 10px; margin: 5px 0; width: 100%; }
        .section { margin-bottom: 2em; border-bottom: 1px solid #ccc; padding-bottom: 1em; }
    </style>
</head>
<body>
<h1>Laravel Auth API Test</h1>

<div class="section">
    <h2>Register</h2>
    <input type="text" id="reg_name" placeholder="Name">
    <input type="email" id="reg_email" placeholder="Email">
    <input type="password" id="reg_password" placeholder="Password">
    <input type="password" id="reg_password_confirmation" placeholder="Confirm Password">
    <button onclick="register()">Register</button>
</div>

<div class="section">
    <h2>Login</h2>
    <input type="email" id="login_email" placeholder="Email">
    <input type="password" id="login_password" placeholder="Password">
    <button onclick="login()">Login</button>
</div>

<div class="section">
    <input type="text" id="url_video" placeholder="name video">
    <button onclick="update_video()">Register</button>

    <video controls width="600">
        <source id="video_source" src="http://localhost:8000/api/video/video.mp4" type="video/mp4">
    </video>
</div>

<div class="section">
    <h2>Google Login</h2>
    <a href="/api/auth/google/redirect">
        <button>Login with Google</button>
    </a>
</div>

<div class="section">
    <h2>Logout</h2>
    <button onclick="logout()">Logout</button>
</div>

<h1>Восстановление пароля</h1>
<form method="POST" action="/api/forgot-password">
    <input type="email" name="email" placeholder="Email для восстановления"><br>
    <button type="submit">Отправить ссылку</button>
</form>

<h1>Сброс пароля</h1>
<form method="POST" action="/api/reset-password">
    <input type="email" name="email" placeholder="Email"><br>
    <input type="text" name="token" placeholder="Token из письма"><br>
    <input type="password" name="password" placeholder="Новый пароль"><br>
    <input type="password" name="password_confirmation" placeholder="Подтверждение"><br>
    <button type="submit">Сбросить пароль</button>
</form>

<pre id="output"></pre>

<script>
    let token = '';
    function update_video() {

        document.getElementById('video_source').src=  document.getElementById('url_video').value;


    }
    function register() {
        fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: document.getElementById('reg_name').value,
                email: document.getElementById('reg_email').value,
                password: document.getElementById('reg_password').value,
                password_confirmation: document.getElementById('reg_password_confirmation').value
            })
        })
            .then(res => res.json())
            .then(data => {
                token = data.token || '';
                document.getElementById('output').textContent = JSON.stringify(data, null, 2);
            });
    }

    function login() {
        fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: document.getElementById('login_email').value,
                password: document.getElementById('login_password').value
            })
        })
            .then(res => res.json())
            .then(data => {
                token = data.token || '';
                document.getElementById('output').textContent = JSON.stringify(data, null, 2);
            });
    }

    function logout() {
        fetch('/api/logout', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                token = '';
                document.getElementById('output').textContent = JSON.stringify(data, null, 2);
            });
    }
</script>
</body>
</html>
