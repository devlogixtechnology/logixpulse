<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>REC-BE-02 Login</title>
<style>
body{font-family:Arial,sans-serif;max-width:520px;margin:40px auto;padding:20px}
input,button{width:100%;padding:10px;margin:7px 0;box-sizing:border-box}
button{cursor:pointer}
pre{background:#f4f4f4;padding:12px;white-space:pre-wrap}
</style>
</head>
<body>
<h1>Session Authentication Demo</h1>
<p>Use the demo accounts from the README.</p>
<form id="loginForm">
<input id="email" type="email" placeholder="Email" required>
<input id="password" type="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>
<pre id="output"></pre>
<script>
document.getElementById('loginForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    const response = await fetch('../api/login.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        })
    });
    document.getElementById('output').textContent =
        JSON.stringify(await response.json(), null, 2);
});
</script>
</body>
</html>
