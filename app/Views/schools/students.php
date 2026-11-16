<div class="data">
    <div class="form">
        <form action="/school/students/save" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($student['id'] ?? '') ?>">

            <label for="adm">Admission No</label>
            <input type="text" name="adm" id="adm" placeholder="ADM001" value="<?= htmlspecialchars($student['adm'] ?? '') ?>" required>

            <label for="upi">UPI (Optional)</label>
            <input type="text" name="upi" id="upi" placeholder="NEMIS UPI" value="<?= htmlspecialchars($student['upi'] ?? '') ?>">

            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" placeholder="Jane Doe" value="<?= htmlspecialchars($student['name'] ?? '') ?>" required>

            <label for="dob">Date of Birth</label>
            <input type="date" name="dob" id="dob" value="<?= htmlspecialchars($student['dob'] ?? '') ?>" required>

            <label for="doa">Date of Admission</label>
            <input type="date" name="doa" id="doa" value="<?= htmlspecialchars($student['doa'] ?? date('Y-m-d')) ?>" required>

            <label for="guardianName">Guardian Name</label>
            <input type="text" name="guardianName" id="guardianName" placeholder="John Doe" value="<?= htmlspecialchars($student['guardian'] ?? '') ?>" required>

            <label for="phone">Guardian Phone</label>
            <input type="text" name="phone" id="phone" placeholder="0712345678" value="<?= htmlspecialchars($student['phone'] ?? '') ?>" required>

            <label for="status">Status</label>
            <select name="status" id="status">
                <?php $currentStatus = $student['status'] ?? 'active'; ?>
                <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="graduated" <?= $currentStatus === 'graduated' ? 'selected' : '' ?>>Graduated</option>
                <option value="transferred" <?= $currentStatus === 'transferred' ? 'selected' : '' ?>>Transferred</option>
            </select>

            <label for="profile_photo">Profile Picture</label>
            <input type="file" name="profile_photo" id="profile_photo">

            <button type="submit"><?= (isset($student) && !empty($student)) ? 'Update' : 'Save' ?></button>
        </form>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Profile</th>
                    <th>Adm No</th>
                    <th>UPI</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Guardian</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($students) && !empty($students)): ?>
                    <?php $today = new DateTimeImmutable('today'); ?>
                    <?php foreach ($students as $index => $std): ?>
                        <?php
                        $age = 'N/A';
                        if (!empty($std['dob'])) {
                            try {
                                $age = (new DateTimeImmutable($std['dob']))->diff($today)->y . ' yrs';
                            } catch (Exception $e) {
                                $age = 'N/A';
                            }
                        }
                        ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php if (!empty($std['profile_photo'])): ?>
                                    <img src="/uploads/<?= htmlspecialchars($std['profile_photo']) ?>" alt="Student Photo">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($std['adm'] ?? '') ?></td>
                            <td><?= htmlspecialchars($std['upi'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($std['name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($age) ?></td>
                            <td><?= htmlspecialchars($std['guardian'] ?? '') ?></td>
                            <td><?= htmlspecialchars($std['phone'] ?? '') ?></td>
                            <td><?= htmlspecialchars(ucfirst($std['status'] ?? 'active')) ?></td>
                            <td>
                                <a href="/school/students?id=<?= htmlspecialchars($std['id']) ?>">Edit</a>
                                <form action="/school/students/delete" method="post" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($std['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
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