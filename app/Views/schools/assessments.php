<div class="data">
    <div class="form">
        <form id="assessments" action="/school/assessments/save" method="post">
            <input id="token" type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($assessment['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

            <label for="type">Assessment Type</label>
            <select name="type" id="type" required>
                <?php $currentType = $assessment['type'] ?? 'formative'; ?>
                <option value="formative" <?= $currentType === 'formative' ? 'selected' : '' ?>>Formative</option>
                <option value="summative" <?= $currentType === 'summative' ? 'selected' : '' ?>>Summative</option>
            </select>

            <label for="start_date">Start Date</label>
            <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars((string) ($assessment['start_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>

            <label for="end_date">End Date</label>
            <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars((string) ($assessment['end_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>

            <label for="status">Status</label>
            <select name="status" id="status" required>
                <?php $currentStatus = $assessment['status'] ?? 'active'; ?>
                <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="completed" <?= $currentStatus === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>

            <button type="submit" id="register">
                <?= (isset($assessment) && !empty($assessment)) ? 'Update Assessment' : 'Save Assessment' ?>
            </button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($assessments) && !empty($assessments)): ?>
                    <?php foreach ($assessments as $index => $row): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars(ucfirst((string) ($row['type'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($row['start_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($row['end_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars(ucfirst((string) ($row['status'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="/school/assessments?id=<?= htmlspecialchars((string) ($row['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                <form action="/school/assessments/delete" method="post" onsubmit="return confirm('Are you sure you want to delete this assessment?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($row['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No assessments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>