<div class="data">
    <div class="form">
        <form id="strands">
            <input id="token" type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($strand['id'] ?? '') ?>">

            <label for="subject">Subjects</label>
            <select name="subject_id" id="subject_id">
                <option value="">-- Select Subject --</option>
                <?php if (isset($subjects) && !empty($subjects)): ?>
                    <?php foreach ($subjects as $sub): ?>
                        <option value="<?= $sub['id'] ?>" <?= (isset($selectedSubject) && $selectedSubject == $sub['id']) ? 'selected' : '' ?>><?= htmlspecialchars($sub['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <button type="submit" id="viewStrands">View Strands</button>


            <label for="strand">Strand Name</label>
            <input type="text" name="name" id="name" placeholder="E.g Introduction to Science" value="<?= htmlspecialchars($strand['name'] ?? '') ?>">
            <button type="submit" id="register"><?= (isset($strand) && !empty($strand)) ? 'Update Strand' : 'Save Strand' ?></button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Strand</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($strands) && !empty($strands)): ?>
                    <?php foreach ($strands as $index => $str): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($str['name']) ?></td>
                            <td>
                                <a href="/school/strands?id=<?= $str['id'] ?>">Edit</a>
                                <form action="/school/strands/delete" method="post" onsubmit="return confirm('Are you sure you want to delete this strand?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($str['token'] ?? '') ?>">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($str['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No strands found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>