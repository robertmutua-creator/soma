<div class="data">
    <div class="form">
        <form id="demoteForm" action="/school/demotions" method="POST">
            <input type="hidden" name="_token" value="<?= htmlspecialchars((string) ($token ?? ''), ENT_QUOTES, 'UTF-8') ?>">

            <label for="fromYear">From Year</label>
            <select name="fromYear" id="fromYear">
                <option value="">-- Select From Year --</option>
                <?php if (!empty($years)): ?>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= htmlspecialchars((string) $year['id'], ENT_QUOTES, 'UTF-8') ?>" <?= ((string) ($selectedFromYear ?? '') === (string) $year['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $year['year'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="fromClass">From Class</label>
            <select name="fromClass" id="fromClass">
                <option value="">-- Select From Class --</option>
                <?php if (!empty($classes)): ?>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= htmlspecialchars((string) $class['id'], ENT_QUOTES, 'UTF-8') ?>" <?= ((string) ($selectedFromClass ?? '') === (string) $class['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $class['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <button type="submit" id="viewStudents">View Students</button>

            <label for="toYear">To Year</label>
            <select name="toYear" id="toYear">
                <option value="">-- Select To Year --</option>
                <?php if (!empty($years)): ?>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= htmlspecialchars((string) $year['id'], ENT_QUOTES, 'UTF-8') ?>" <?= ((string) ($selectedToYear ?? '') === (string) $year['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $year['year'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="toClass">To Class</label>
            <select name="toClass" id="toClass">
                <option value="">-- Select To Class --</option>
                <?php if (!empty($classes)): ?>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= htmlspecialchars((string) $class['id'], ENT_QUOTES, 'UTF-8') ?>" <?= ((string) ($selectedToClass ?? '') === (string) $class['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $class['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <button type="submit" id="demote">Demote</button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th colspan="100%">Available Students</th>
                </tr>
                <tr>
                    <th style="width: 30px; text-align:center">
                        <input type="checkbox" name="selectAll" id="selectAll">
                    </th>
                    <th>#</th>
                    <th>Adm</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $index => $student): ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="student_ids[]" value="<?= htmlspecialchars((string) $student['id'], ENT_QUOTES, 'UTF-8') ?>" class="checkbox" form="demoteForm">
                            </td>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars((string) $student['adm'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $student['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?= htmlspecialchars((string) ($student['enrollment_status'] ?? $student['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No student found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>