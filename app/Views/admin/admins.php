<div class="data">
    <div class="form">
        <form action="/admins/save" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">

            <input type="hidden" name="id" value="<?= htmlspecialchars($admin['id'] ?? '') ?>">

            <label for="">School Admin Name</label>
            <input type="text" name="name" id="name" placeholder="Mr Mutua" value="<?= htmlspecialchars($admin['name'] ?? '') ?>">

            <label for="">Username</label>
            <input type="text" name="username" id="code" placeholder="eg tsc no" value="<?= htmlspecialchars($admin['username'] ?? '') ?>">

            <label for="">School Admin Profile</label>
            <input type="file" name="profile" id="profile">
        </form>
    </div>
</div>