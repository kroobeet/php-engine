<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="/resources/css/home.css"> <!-- Подключение стилей для главной страницы -->
</head>
<body>
<div class="container">
    <header class="header">
        <h1><?= $title; ?></h1>
        <nav>
            <ul class="nav-links">
                <?php if (!$session): ?>
                    <li><a href="/login">Login</a></li>
                    <li><a href="/register">Register</a></li>
                <?php else: ?>
                    <li><a href="/dashboard">Dashboard</a></li>
                    <li><a href="/logout">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="main-content">
        <p>Welcome to our website!</p>
    </main>
    <footer class="footer">
        <p>&copy; 2024 Your Company</p>
    </footer>
</div>
</body>
</html>
