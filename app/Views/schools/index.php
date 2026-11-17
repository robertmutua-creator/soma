<div class="cards">
    <div class="card">
        <h4>Academic Year</h4>
        <h3><?= htmlspecialchars($currentTerm[0]['name'] ?? 'Term Not Set') . '/' . htmlspecialchars($currentYear[0]['year'] ?? 'Year Not Set') ?></h3>
        <p>Current Academic Year</p>
    </div>
    <div class="card">
        <h4>Students</h4>
        <h3><?= htmlspecialchars($totalStudents ?? '--') ?></h3>
        <p>Total Active Students</p>
    </div>

    <div class="card">
        <h4>Teachers</h4>
        <h3><?= htmlspecialchars($totalTeachers ?? '--') ?></h3>
        <p>Total Active Teachers</p>
    </div>

    <div class="card">
        <h4>Streams</h4>
        <h3><?= htmlspecialchars($totalStreams ?? '--') ?></h3>
        <p>Total Occupied Streams</p>
    </div>

    <div class="card">
        <h4>Subjects</h4>
        <h3><?= htmlspecialchars($totalSubjects ?? '--') ?></h3>
        <p>Total Subjects Done</p>
    </div>
</div>