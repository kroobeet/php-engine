<!-- views/login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/resources/css/auth.css"> <!-- Подключение стилей -->
</head>
<body>

<div class="container">
    <h1>Login</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="post" action="/login">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Login">
    </form>

    <div class="register-link">
        <p>Don't have an account? <a href="/register">Register here</a></p>
    </div>
</div>

</body>
</html>
