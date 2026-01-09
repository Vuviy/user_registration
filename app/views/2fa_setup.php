<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>2FA Setup</title>
</head>
<body>

<h2>Set up Two-Factor Authentication</h2>

<p>
    Scan this QR code with <strong>Google Authenticator</strong>
    or any compatible TOTP app.
</p>

<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($qr) ?>"
     alt="2FA QR Code">

<form method="POST" action="/2fa/setup">
    <label>
        Enter 6-digit code from app:
        <input type="text" name="code" required pattern="[0-9]{6}">
    </label>
    <br><br>
    <button type="submit">Confirm 2FA</button>
</form>

</body>
</html>
