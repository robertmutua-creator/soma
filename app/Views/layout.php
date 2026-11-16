<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Soma') ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>

<body>
    <header>
        <div><?= htmlspecialchars($user['name'] ?? 'Soma') ?></div>
        <div><img src="/uploads/<?= htmlspecialchars($user['profile'] ?? 'avatar.jpeg') ?>" alt=""></div>
    </header>
    <main>
        <?php if (isset($user) && isset($links)): ?>
            <nav aria-label="Main navigation">
                <?php foreach ($links as $link): ?>
                    <?php if ($link['link'] === '/logout'): ?>
                        <form action="/logout" method="post">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                            <button type="submit"><?= htmlspecialchars($link['label']) ?></button>
                        </form>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars($link['link']) ?>"><?= htmlspecialchars($link['label']) ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <h3><?= htmlspecialchars($title ?? 'Soma Module') ?></h3>
        <?php
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        if (is_array($flash) && isset($flash['type'], $flash['message'])):
        ?>
            <p role="alert"><?= htmlspecialchars($flash['message']) ?></p>
        <?php endif; ?>
        <?php
        if (isset($content) && !empty($content)) {
            require dirname(__DIR__) . '/Views/' . $content . '.php';
        } else {
            echo 'Content Not Set';
        }
        ?>
    </main>

    <footer>
        Software by MutuaSoftwares <?= date('Y') ?>
        <script src="/assets/scripts/app.js"></script>
    </footer>
</body>

</html>