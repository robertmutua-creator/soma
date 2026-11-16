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
        <div><img src="/uploads/<?= htmlspecialchars($user['profile_photo'] ?? 'avatar.jpeg') ?>" alt=""></div>
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

        <div class="content">
            <h2><?= htmlspecialchars($title ?? 'Soma Module') ?></h2>
            <?php
            if (isset($_SESSION['flash'])) {
                $flash = $_SESSION['flash'];

                echo "<p class='alert " . htmlspecialchars($flash['type']) . "'>"
                    . htmlspecialchars($flash['message']) . "</p>";

                unset($_SESSION['flash']);
            }
            ?>
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
        Software by MutuaSoftwares <?= date('Y') ?>
        <script src="/assets/scripts/app.js"></script>
    </footer>
</body>

</html>