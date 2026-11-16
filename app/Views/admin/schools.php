<div class="data">
    <div class="form">
        <form action="/schools/save" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($school['id'] ?? '') ?>">
            <label for="">School Code</label>
            <input type="text" name="code" id="code" placeholder="SCH001" value="<?= htmlspecialchars($school['code'] ?? '') ?>">
            <label for="">School Name</label>
            <input type="text" name="name" id="name" placeholder="School One" value="<?= htmlspecialchars($school['name'] ?? '') ?>">
            <label for="">Address</label>
            <input type="text" name="address" id="address" placeholder="P.O. Box 001 Phone 0712...." value="<?= htmlspecialchars($school['address'] ?? '') ?>">
            <label for="">School Logo</label>
            <input type="file" name="logo" id="logo">
            <label for="">Status</label>
            <select name="status" id="status">
                <option value="active" <?= (isset($school) && $school['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= (isset($school) && $school['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>
            <button type="submit"><?= isset($school) ? 'Update' : 'Save' ?></button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>#Logo</th>
                    <th>#Code</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($schools) && !empty($schools)): ?>
                    <?php foreach ($schools as $index => $sch): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td>
                                <?php if (!empty($sch['logo'])): ?>
                                    <img src="/uploads/<?= htmlspecialchars($sch['logo']) ?>" alt="School Logo">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($sch['code']) ?></td>
                            <td><?= htmlspecialchars($sch['name']) ?></td>
                            <td><?= htmlspecialchars($sch['address']) ?></td>
                            <td><?= htmlspecialchars($sch['status']) ?></td>
                            <td>
                                <a href="/schools?id=<?= htmlspecialchars($sch['id']) ?>">Edit</a>
                                <form action="/schools/delete" method="post">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($sch['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No schools found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>