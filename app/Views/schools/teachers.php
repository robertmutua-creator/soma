<div class="data">
    <div class="form">
        <form action="/school/teachers/save" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($teacher['id'] ?? '') ?>">

            <label for="">Employment No</label>
            <input type="text" name="username" placeholder="1234" value="<?= htmlspecialchars($teacher['username'] ?? '') ?>">

            <label for="">Name</label>
            <input type="text" name="name" id="name" placeholder="Mr Mutua" value="<?= htmlspecialchars($teacher['name'] ?? '') ?>">

            <label for="">Phone</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>">

            <label for="">Profile Picture</label>
            <input type="file" name="profile_photo" id="profile_photo">
            <button type="submit"><?= (isset($teacher) && !empty($teacher)) ? 'Update' : 'Save' ?></button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Profile</th>
                    <th>Employment No</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($teachers) && !empty($teachers)): ?>
                    <?php foreach ($teachers as $index => $tch): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php if (!empty($tch['profile_photo'])): ?>
                                    <img src="/uploads/<?= htmlspecialchars($tch['profile_photo']) ?>" alt="Teacher Logo">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($tch['username']) ?></td>
                            <td><?= htmlspecialchars($tch['name']) ?></td>
                            <td><?= htmlspecialchars($tch['phone']) ?></td>
                            <td>
                                <a href="/school/teachers?id=<?= htmlspecialchars($tch['id']) ?>">Edit</a>
                                <form action="/school/teachers/delete" method="post" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($tch['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No teacher found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>