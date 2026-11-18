<div class="data">
    <div class="form">
        <form action="/school/allocations/save" method="post">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">

            <label for="year_id">Academic Year</label>
            <select name="year_id" id="year_id" required>
                <?php if (isset($years) && !empty($years)): ?>
                    <?php foreach ($years as $yr): ?>
                        <option value="<?= $yr['id'] ?>" <?= (isset($currentYearId) && $currentYearId == $yr['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($yr['year']) ?> (<?= ucfirst($yr['status']) ?>)
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No academic years found</option>
                <?php endif; ?>
            </select>

            <label for="class_id">Class</label>
            <select name="class_id" id="class_id" required>
                <option value="">Select Class</option>
                <?php if (isset($classes) && !empty($classes)): ?>
                    <?php foreach ($classes as $cls): ?>
                        <option value="<?= $cls['id'] ?>"><?= htmlspecialchars($cls['name']) ?> (<?= htmlspecialchars($cls['code']) ?>)</option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" required>
                <option value="">Select Subject</option>
                <?php if (isset($subjects) && !empty($subjects)): ?>
                    <?php foreach ($subjects as $sub): ?>
                        <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['name']) ?> (<?= htmlspecialchars($sub['code']) ?>)</option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="teacher_id">Teacher</label>
            <select name="teacher_id" id="teacher_id" required>
                <option value="">Select Teacher</option>
                <?php if (isset($teachers) && !empty($teachers)): ?>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="from_date">Assignment Date</label>
            <input type="date" name="from_date" id="from_date" value="<?= date('Y-m-d') ?>" required>

            <button type="submit">Assign Teacher</button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Class</th>
                    <th>Subject</th>
                    <th>Teacher</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($allocations) && !empty($allocations)): ?>
                    <?php foreach ($allocations as $index => $alloc): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($alloc['class_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alloc['subject_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alloc['teacher_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alloc['from_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alloc['to_date'] ?? 'Present') ?></td>
                            <td><?= htmlspecialchars(ucfirst($alloc['status'] ?? 'active')) ?></td>
                            <td>
                                <form action="/school/allocations/delete" method="post" onsubmit="return confirm('Are you sure you want to remove this allocation?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($alloc['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No teacher allocations found for this year.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>