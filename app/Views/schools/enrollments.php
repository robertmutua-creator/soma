<div class="data">
    <div class="form">
        <form action="/school/enrollments/store" method="post">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">

            <label for="year_id">Academic Year</label>
            <select name="year_id" id="year_id" required>
                <option value="">-- Select Year --</option>
                <?php if (isset($years) && !empty($years)): ?>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= $year['id'] ?>" <?= (isset($currentYearId) && $currentYearId === $year['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($year['year']) ?> (<?= ucfirst($year['status']) ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="class_id">Target Class</label>
            <select name="class_id" id="class_id" required>
                <option value="">-- Select Class --</option>
                <?php if (isset($classes) && !empty($classes)): ?>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>">
                            <?= htmlspecialchars($class['name']) ?> (<?= htmlspecialchars($class['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="enrollment_date">Enrollment Date</label>
            <input type="date" name="enrollment_date" id="enrollment_date" value="<?= date('Y-m-d') ?>" required>

            <p style="margin-top: 15px; font-size: 0.9rem; color: #666;">
                <em>Select unenrolled students from the table, then click assign.</em>
            </p>
            <button type="submit">Assign Selected Students</button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th>#</th>
                    <th>Adm No</th>
                    <th>UPI</th>
                    <th>Full Name</th>
                    <th>Guardian</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($unenrolledStudents) && !empty($unenrolledStudents)): ?>
                    <?php foreach ($unenrolledStudents as $index => $student): ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="student_ids[]" value="<?= $student['id'] ?>" class="student-checkbox" form="enrollmentForm">
                            </td>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($student['adm'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($student['upi'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($student['name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($student['guardian'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($student['phone'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No un-enrolled students found. All registered students are assigned to classes.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>