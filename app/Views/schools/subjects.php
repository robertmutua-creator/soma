<div class="data">
    <div class="form">
        <form action="/school/subjects/save" method="post">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($subject['id'] ?? '') ?>">

            <label for="">Subject Code</label>
            <input type="text" name="code" id="code" placeholder="ENG" value="<?= htmlspecialchars($subject['code'] ?? '') ?>">

            <label for="">Subject Name</label>
            <input type="text" name="name" id="name" placeholder="English" value="<?= htmlspecialchars($subject['name'] ?? '') ?>">

            <button type="submit"><?= isset($subject) ? 'Update' : 'Save' ?></button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>#Code</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($subjects) && !empty($subjects)): ?>
                    <?php foreach ($subjects as $index => $sbj): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($sbj['code']) ?></td>
                            <td><?= htmlspecialchars($sbj['name']) ?></td>
                            <td>
                                <a href="/school/subjects?id=<?= htmlspecialchars($sbj['id']) ?>">Edit</a>
                                <form action="/school/subjects/delete" method="post" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($sbj['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No subjects found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>