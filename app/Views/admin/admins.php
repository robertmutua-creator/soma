<div class="data">
    <div class="form">
        <form action="/admins/save" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">

            <input type="hidden" name="id" value="<?= htmlspecialchars($admin['id'] ?? '') ?>">

            <label for="">Select School</label>
            <select name="school_id" id="school_id">
                <option value="">Select School</option>
                <?php if (isset($schools)): ?>
                    <?php foreach ($schools as $sch): ?>
                        <option value="<?= $sch['id'] ?>" <?= (isset($admin) && $admin['school_id'] == $sch['id']) ? 'selected' : '' ?>><?= htmlspecialchars($sch['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <label for="">School Admin Name</label>
            <input type="text" name="name" id="name" placeholder="Mr Mutua" value="<?= htmlspecialchars($admin['name'] ?? '') ?>">

            <label for="">Username</label>
            <input type="text" name="username" id="code" placeholder="eg tsc no" value="<?= htmlspecialchars($admin['username'] ?? '') ?>">

            <label for="">School Admin Profile</label>
            <input type="file" name="profile" id="profile">
            <button type="submit"><?= (isset($admin) && !empty($admin)) ? 'Update' : 'Save' ?></button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Profile</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>School</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($admins) && !empty($admins)): ?>
                    <?php foreach ($admins as $index => $adm): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td>
                                <?php if (!empty($adm['profile_photo'])): ?>
                                    <img src="/uploads/<?= htmlspecialchars($adm['profile_photo']) ?>" alt="Admin Logo">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($adm['username']) ?></td>
                            <td><?= htmlspecialchars($adm['name']) ?></td>
                            <td><?= htmlspecialchars($adm['role']) ?></td>
                            <td><?= htmlspecialchars($adm['school']) ?></td>
                            <td>
                                <a href="/admins?id=<?= htmlspecialchars($adm['id']) ?>">Edit</a>
                                <form action="/admins/delete" method="post" onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($adm['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No admin found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>