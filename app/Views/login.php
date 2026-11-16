<div class="login">
    <form action="/login" method="post">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
        <label for="">Username</label>
        <input type="text" name="username" id="username" placeholder="Username">
        <label for="">Password</label>
        <input type="password" name="password" id="password" placeholder="Password">
        <button type="submit">Login</button>
    </form>
</div>