<div class="data">
    <div class="form">
        <form id="substrands" action="/school/substrands" method="get">
            <input id="token" type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($substrand['id'] ?? '') ?>">

            <label for="subject_id">Subjects</label>
            <select name="subject_id" id="subject_id" onchange="getStrands()">
                <option value="">-- Select Subject --</option>
                <?php if (isset($subjects) && !empty($subjects)): ?>
                    <?php foreach ($subjects as $sub): ?>
                        <option value="<?= htmlspecialchars((string) $sub['id'], ENT_QUOTES, 'UTF-8') ?>" <?= (isset($selectedSubject) && $selectedSubject == $sub['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sub['name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="strand_id">Strands</label>
            <select name="strand_id" id="strand_id" onchange="getSubstrands()">
                <option value="">-- Select Strand --</option>
                <?php if (isset($strands) && !empty($strands)): ?>
                    <?php foreach ($strands as $str): ?>
                        <option value="<?= htmlspecialchars((string) $str['id'], ENT_QUOTES, 'UTF-8') ?>" <?= (isset($selectedStrand) && $selectedStrand == $str['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($str['name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="name">Substrand Name</label>
            <input type="text" name="name" id="name" placeholder="E.g Scientific Investigation" value="<?= htmlspecialchars($substrand['name'] ?? '') ?>">
            <button type="button" id="register" onclick="saveSubstrand()">
                <?= (isset($substrand) && !empty($substrand)) ? 'Update Substrand' : 'Save Substrand' ?>
            </button>
        </form>
    </div>

    <div class=" table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Substrand</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($substrands) && !empty($substrands)): ?>
                    <?php foreach ($substrands as $index => $subStrand): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($subStrand['name']) ?></td>
                            <td>
                                <a href="/school/substrands?id=<?= htmlspecialchars((string) $subStrand['id'], ENT_QUOTES, 'UTF-8') ?>&subject_id=<?= htmlspecialchars((string) ($selectedSubject ?? ''), ENT_QUOTES, 'UTF-8') ?>&strand_id=<?= htmlspecialchars((string) ($selectedStrand ?? ''), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                <form action="/school/substrands/delete" method="post" onsubmit="return confirm('Are you sure you want to delete this substrand?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $subStrand['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No substrands found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>