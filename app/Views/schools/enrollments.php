<div class="data">
    <div class="form">
        <form id="enrollmentForm">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>" id="token">

            <label for="year_id">Select Year</label>
            <select name="year_id" id="year_id">
                <?php if (isset($years) && !empty($years)): ?>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= htmlspecialchars($year['id']) ?>" <?= (isset($currentYearId) && $currentYearId == $year['id']) ? 'selected' : '' ?>><?= htmlspecialchars($year['year']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="class_id">Select Class</label>
            <select name="class_id" id="class_id">
                <?php if (isset($classes) && !empty($classes)): ?>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= htmlspecialchars($class['id']) ?>" <?= (isset($currentClassId) && $currentClassId == $class['id']) ? 'selected' : '' ?>><?= htmlspecialchars($class['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <div class="btns" style="display: flex; justify-content:space-between">
                <button type="submit" id="enroll">Enroll Selected</button>
                <button type="submit" id="view">View Enrolled</button>
            </div>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th colspan="100%">List of Enrolled Students (<?= htmlspecialchars($enrolledCount ?? '') ?>)</th>
                </tr>
                <tr>
                    <th>#</th>
                    <th>Adm</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($enrolled) && !empty($enrolled)): ?>
                    <?php foreach ($enrolled as $index => $enr): ?>
                        <tr>
                            <td><?= htmlspecialchars($index + 1) ?></td>
                            <td><?= htmlspecialchars($enr['adm']) ?></td>
                            <td><?= htmlspecialchars($enr['name']) ?></td>
                            <td>
                                <?php
                                $statuses = ['active', 'transferred', 'completed', 'dropped'];
                                $rowStatus = (string) ($enr['status'] ?? $enr['enrollment_status'] ?? 'active');
                                $yearFilter = (string) ($currentYearId ?? $enr['year_id'] ?? '');
                                $classFilter = (string) ($currentClassId ?? $enr['class_id'] ?? '');
                                ?>
                                <form action="/school/enrollments/status" method="post" style="display: inline-flex; gap: 6px; align-items: center; margin: 0;">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars((string) ($token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($enr['enrollment_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="year_id" value="<?= htmlspecialchars($yearFilter, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="class_id" value="<?= htmlspecialchars($classFilter, ENT_QUOTES, 'UTF-8') ?>">

                                    <select
                                        name="status"
                                        data-prev-status="<?= htmlspecialchars($rowStatus, ENT_QUOTES, 'UTF-8') ?>"
                                        aria-label="Enrollment status for <?= htmlspecialchars((string) ($enr['name'] ?? 'student'), ENT_QUOTES, 'UTF-8') ?>"
                                        onchange="if (confirm('Change status to ' + this.value + '?')) { this.form.submit(); } else { this.value = this.dataset.prevStatus; }">
                                        <?php foreach ($statuses as $statusVal): ?>
                                            <option value="<?= htmlspecialchars($statusVal, ENT_QUOTES, 'UTF-8') ?>" <?= ($rowStatus === $statusVal) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars(ucfirst($statusVal), ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No students enrolled to this class in the selected year.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="100%">List of Unenrolled Students (<?= htmlspecialchars((string) ($unenrolledCount ?? count($unenrolled ?? []))) ?>)</th>
                </tr>
                <tr>
                    <th style="width: 30px; text-align: center;">
                        <input type="checkbox" name="selectAllUnenrolled" id="selectAllUnenrolled" aria-label="Select all unenrolled students">
                    </th>
                    <th>#</th>
                    <th>Adm</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($unenrolled) && !empty($unenrolled)): ?>
                    <?php foreach ($unenrolled as $row => $unr): ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="student_ids[]" value="<?= htmlspecialchars($unr['id']) ?>" class="unenrolled-checkbox" form="enrollmentForm">
                            </td>
                            <td><?= $row + 1 ?></td>
                            <td><?= htmlspecialchars($unr['adm']) ?></td>
                            <td><?= htmlspecialchars($unr['name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">No unenrolled student found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>