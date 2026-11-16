<div class="data">
    <div class="form">
        <form action="/school/classes/save" method="post">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($class['id'] ?? '') ?>">

            <label for="">Class Code</label>
            <input type="text" name="code" id="code" placeholder="G10" value="<?= htmlspecialchars($class['code'] ?? '') ?>">

            <label for="">Class Name</label>
            <input type="text" name="name" id="name" placeholder="Grade Ten" value="<?= htmlspecialchars($class['name'] ?? '') ?>">

            <button type="submit"><?= isset($class) ? 'Update' : 'Save' ?></button>
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
                <?php if (isset($classes) && !empty($classes)): ?>
                    <?php foreach ($classes as $index => $cls): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($cls['code']) ?></td>
                            <td><?= htmlspecialchars($cls['name']) ?></td>
                            <td>
                                <a href="/school/classes?id=<?= htmlspecialchars($cls['id']) ?>">Edit</a>
                                <form action="/school/classes/delete" method="post" onsubmit="return confirm('Are you sure you want to delete this class?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($cls['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No classes found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>