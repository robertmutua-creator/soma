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
                        <option value="<?= $sch['id'] ?>"><?= htmlspecialchars($sch['name']) ?></option>
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
</div>