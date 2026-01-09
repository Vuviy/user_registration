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
    <?php if ($user): ?>
    user : <?php echo $user['email']; ?>
    <a href="/logout">logout</a>
    <?php endif; ?>
</div>

<div>
    <h1>Register</h1>
    <a href="/form">register</a>
</div>

<div>
    <h1>about</h1>
    <a href="/about">about</a>
</div>
</body>
</html>