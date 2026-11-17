<div class="data">
    <div class="form">
        <form action="/school/terms/save" method="post">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($term['id'] ?? '') ?>">

            <label for="">Select Academic Year</label>
            <select name="year_id" id="year_id">
                <option value="">Select Academic Year</option>
                <?php if (isset($years)): ?>
                    <?php foreach ($years as $yr): ?>
                        <option value="<?= $yr['id'] ?>" <?= (isset($term) && $term['year_id'] == $yr['id']) ? 'selected' : '' ?>><?= htmlspecialchars($yr['year']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="">Term Name</label>
            <input type="text" name="name" id="name" placeholder="Term 1" value="<?= htmlspecialchars($term['name'] ?? '') ?>">

            <label for="">Start Date</label>
            <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($term['start_date'] ?? '') ?>">

            <label for="">End Date</label>
            <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($term['end_date'] ?? '') ?>">

            <label for="">Status</label>
            <select name="status" id="status">
                <?php $currentStatus = $term['status'] ?? 'upcoming'; ?>
                <option value="upcoming" <?= $currentStatus === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="closed" <?= $currentStatus === 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>

            <button type="submit"><?= (isset($term) && !empty($term)) ? 'Update' : 'Save' ?></button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Academic Year</th>
                    <th>Term</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($terms) && !empty($terms)): ?>
                    <?php $yearMap = isset($years) ? array_column($years, 'year', 'id') : []; ?>
                    <?php foreach ($terms as $index => $trm): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($yearMap[$trm['year_id']] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($trm['name']) ?></td>
                            <td><?= htmlspecialchars($trm['start_date'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($trm['end_date'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars(ucfirst($trm['status'])) ?></td>
                            <td>
                                <a href="/school/terms?id=<?= htmlspecialchars($trm['id']) ?>">Edit</a>
                                <form action="/school/terms/delete" method="post" onsubmit="return confirm('Are you sure you want to delete this term?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($trm['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No terms found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>