<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="/resources/css/dashboard.css"> <!-- Подключение стилей -->
</head>
<body>

<div class="sidebar">
    <div>
        <h2>Dashboard</h2>
        <ul>
            <li><a href="<?= $router->getUrl('/dashboard'); ?>">Go to Dashboard</a></li>
            <li><a href="<?= $router->getUrl('/'); ?>">Home</a></li>
            <li><a href="#">Settings</a></li>
            <li><a href="#">Messages</a></li>
            <li><a href="<?= $router->getUrl('/logout'); ?>">Logout</a></li>
        </ul>
    </div>
    <p>&copy; 2024 ResolveTex</p>
</div>

<div class="main-content">
    <div class="header"><?= $title; ?></div>
    <p class="welcome-text">Hello, <?= $username ?></p>
    <p class="welcome-text">Welcome to your dashboard</p>

    <div class="card">
        <h3>Recent Activity</h3>
        <p>Here you can see the most recent actions on your account.</p>
    </div>

    <div class="card">
        <h3>Account Stats</h3>
        <p>Check your profile statistics and performance here.</p>
    </div>
</div>

</body>
</html>
