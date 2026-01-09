<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>


<div>
    <a href="/">Home</a>
    <h1>Register</h1>

    <form method="POST" action="/register">
        <label for="email">Email</label>
        <input type="text" name="email">
        <label for="password">Password</label>
        <input type="text" name="password">
        <button type="submit">Register</button>
    </form>

    <h1>Login</h1>
    <form method="POST" action="/login">
        <label for="email">Email</label>
        <input type="text" name="email">
        <label for="password">Password</label>
        <input type="text" name="password">
        <button type="submit">Login</button>
    </form>
</div>

<script src="https://www.google.com/recaptcha/api.js?render=<?= RECAPTCHA_SITE_KEY ?>"></script>
<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('<?= RECAPTCHA_SITE_KEY ?>', {action: 'register'}).then(function(token) {
            let form = document.querySelector('form[action="/register"]');
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'g-recaptcha-response';
            input.value = token;
            form.appendChild(input);
        });

        grecaptcha.execute('<?= RECAPTCHA_SITE_KEY ?>', {action: 'login'}).then(function(token) {
            let form = document.querySelector('form[action="/login"]');
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'g-recaptcha-response';
            input.value = token;
            form.appendChild(input);
        });
    });
</script>
</body>
</html>