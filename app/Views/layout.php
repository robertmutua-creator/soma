<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Soma') ?></title>
</head>

<body>
    <header>
        <div class="left"><?= htmlspecialchars($user['name'] ?? 'Soma') ?></div>
        <div class="avatar"><img src="/uploads/<?= htmlspecialchars($user['profile'] ?? 'avatar.jpeg') ?>" alt=""></div>
    </header>
    <main>
        <?php if (isset($user) && isset($links)): ?>
            <div class="sidebar">
                <?php foreach ($links as $link): ?>
                    <a href="<?= htmlspecialchars($link['link']) ?>"><?= htmlspecialchars($link['label']) ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="content">
            <?php
            if (isset($content) && !empty($content)) {
                require dirname(__DIR__) . '/Views/' . $content . '.php';
            } else {
                echo 'Content Not Set';
            }
            ?>
        </div>
    </main>

    <footer>
        <!-- add something here -->
    </footer>
</body>

</html>