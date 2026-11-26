<div class="data">
    <div class="form">
        <form id="performance_levels" action="/school/performance-levels/save" method="post">
            <input id="token" type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($level['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

            <label for="code">Level Code</label>
            <input type="text" name="code" id="code" placeholder="E.g EE" value="<?= htmlspecialchars((string) ($level['code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>

            <label for="label">Level Label</label>
            <input type="text" name="label" id="label" placeholder="E.g Exceeding Expectation" value="<?= htmlspecialchars((string) ($level['label'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>

            <label for="min_mark">Minimum Mark</label>
            <input type="number" step="0.01" min="0" name="min_mark" id="min_mark" placeholder="E.g 80" value="<?= htmlspecialchars((string) ($level['min_mark'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>

            <label for="max_mark">Maximum Mark</label>
            <input type="number" step="0.01" min="0" name="max_mark" id="max_mark" placeholder="E.g 100" value="<?= htmlspecialchars((string) ($level['max_mark'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>

            <button type="submit" id="register">
                <?= (isset($level) && !empty($level)) ? 'Update Performance Level' : 'Save Performance Level' ?>
            </button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Label</th>
                    <th>Mark Range</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($levels) && !empty($levels)): ?>
                    <?php foreach ($levels as $index => $row): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars((string) ($row['code'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($row['label'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?= htmlspecialchars((string) ($row['min_mark'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                -
                                <?= htmlspecialchars((string) ($row['max_mark'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <a href="/school/performance-levels?id=<?= htmlspecialchars((string) ($row['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                <form action="/school/performance-levels/delete" method="post" onsubmit="return confirm('Are you sure you want to delete this performance level?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($row['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No performance levels found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>