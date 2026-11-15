<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Soma') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex flex-column min-vh-100 bg-white text-dark">
    <header class="d-flex justify-content-between align-items-center p-3 bg-dark text-white border-bottom border-dark">
        <div class="left"><?= htmlspecialchars($user['name'] ?? 'Soma') ?></div>
        <div class="avatar"><img class="rounded-circle" src="/uploads/<?= htmlspecialchars($user['profile'] ?? 'avatar.jpeg') ?>" alt=""></div>
    </header>
    <main class="d-flex flex-grow-1 align-items-stretch gap-3 p-3">
        <?php if (isset($user) && isset($links)): ?>
            <div class="sidebar d-flex flex-column align-self-start gap-2 p-3 bg-white border border-dark rounded">
                <?php foreach ($links as $link): ?>
                    <a class="text-dark text-decoration-none" href="<?= htmlspecialchars($link['link']) ?>"><?= htmlspecialchars($link['label']) ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="content d-flex flex-grow-1 flex-column p-3 bg-white border border-dark rounded">
            <div class="title h2 mb-3 text-dark"><?= htmlspecialchars($title ?? 'Soma Module') ?></div>
            <?php
            $flash = $_SESSION['flash'] ?? null;
            unset($_SESSION['flash']);

            if (is_array($flash) && isset($flash['type'], $flash['message'])):
            ?>
                <div class="alert alert-light border border-dark text-dark" role="alert">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>
            <?php
            if (isset($content) && !empty($content)) {
                require dirname(__DIR__) . '/Views/' . $content . '.php';
            } else {
                echo 'Content Not Set';
            }
            ?>
        </div>
    </main>

    <footer class="p-3 bg-dark text-white border-top border-dark">
        Software by MutuaSoftwares <?= date('Y') ?>
    </footer>
</body>

</html>