<div class="data">
    <div class="form">
        <form action="/school/years/save" method="post">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($year['id'] ?? '') ?>">

            <label for="year">Academic Year</label>
            <input type="text" name="year" id="year" placeholder="2026" value="<?= htmlspecialchars($year['year'] ?? '') ?>">

            <label for="status">Status</label>
            <select name="status" id="status">
                <?php $currentStatus = $year['status'] ?? 'active'; ?>
                <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="upcoming" <?= $currentStatus === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                <option value="closed" <?= $currentStatus === 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>

            <button type="submit"><?= isset($year) ? 'Update' : 'Save' ?></button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Academic Year</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($years) && !empty($years)): ?>
                    <?php foreach ($years as $index => $yr): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($yr['year']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($yr['status'])) ?></td>
                            <td>
                                <a href="/school/years?id=<?= htmlspecialchars($yr['id']) ?>">Edit</a>
                                <form action="/school/years/delete" method="post" onsubmit="return confirm('Are you sure you want to delete this academic year?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($yr['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No academic years found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>