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
    <main class="d-flex flex-column flex-md-row flex-grow-1 align-items-stretch gap-3 p-3">
        <?php if (isset($user) && isset($links)): ?>
            <nav class="sidebar col-auto col-md-3 col-lg-2" aria-label="Main navigation">
                <button class="btn btn-outline-dark d-md-none mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarLinks" aria-expanded="false" aria-controls="sidebarLinks">
                    Navigation
                </button>
                <div class="collapse d-md-block" id="sidebarLinks">
                    <div class="p-2 bg-white border border-dark rounded shadow-sm">
                        <div class="small text-uppercase text-secondary fw-semibold px-3 pt-2 pb-1">Menu</div>
                        <div class="nav nav-pills flex-column gap-1">
                            <?php foreach ($links as $link): ?>
                                <a class="nav-link link-dark rounded px-3 py-2" href="<?= htmlspecialchars($link['link']) ?>"><?= htmlspecialchars($link['label']) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </nav>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>