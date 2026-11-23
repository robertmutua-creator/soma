<div class="data">
    <div class="form">
        <form id="promotionForm" action="/school/promotions">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">

            <label for="fromYear">From Year</label>
            <select name="fromYear" id="fromYear">
                <option value="">-- Select From Year --</option>
                <?php if (isset($years) && !empty($years)): ?>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= htmlspecialchars($year['id']) ?>" <?= (isset($selectedFromYear) && $selectedFromYear == $year['id']) ? 'selected' : '' ?>><?= htmlspecialchars($year['year']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <label for="fromClass">From Class</label>
            <select name="fromClass" id="fromClass">
                <option value="">-- Select From Class --</option>
                <?php if (isset($classes) && !empty($classes)): ?>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= htmlspecialchars($class['id']) ?>" <?= (isset($selectedFromClass) && $selectedFromClass == $class['id']) ? 'selected' : '' ?>><?= htmlspecialchars($class['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <button type="submit" id="viewStudents">View Students</button>

            <label for="toYear">To Year</label>
            <select name="toYear" id="toYear">
                <option value="">-- Select To Year --</option>
                <?php if (isset($years) && !empty($years)): ?>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= htmlspecialchars($year['id']) ?>" <?= (isset($selectedToYear) && $selectedToYear == $year['id']) ? 'selected' : '' ?>><?= htmlspecialchars($year['year']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <label for="toClass">To Class</label>
            <select name="toClass" id="toClass">
                <?php if (isset($classes) && !empty($classes)): ?>
                    <option value="">-- Select To Class --</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= htmlspecialchars($class['id']) ?>" <?= (isset($selectedToClass) && $selectedToClass == $class['id']) ? 'selected' : '' ?>><?= htmlspecialchars($class['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <button type="submit" id="promote">Promote</button>
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
                <?php if (isset($students) && !empty($students)): ?>
                    <?php foreach ($students as $index => $student): ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="student_ids[]" value="<?= htmlspecialchars($student['id']) ?>" class="checkbox" form="promotionForm">
                            </td>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($student['adm']) ?></td>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <td>
                                <?= htmlspecialchars($student['enrollment_status']) ?>
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