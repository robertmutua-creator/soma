<div class="row row-cols-1 row-cols-sm-2 g-3">
    <div class="col">
        <div class="card h-100 border-dark shadow-sm">
            <h4 class="card-header bg-dark text-white">Schools</h4>
            <div class="card-body">
                <h3 class="card-title display-6 fw-semibold"><?= htmlspecialchars($totalSchools ?? '--') ?></h3>
                <p class="card-text text-secondary mb-0">Total registered schools.</p>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card h-100 border-dark shadow-sm">
            <h4 class="card-header bg-dark text-white">Users</h4>
            <div class="card-body">
                <h3 class="card-title display-6 fw-semibold"><?= htmlspecialchars($totalUsers ?? '--') ?></h3>
                <p class="card-text text-secondary mb-0">Total System Users</p>
            </div>
        </div>
    </div>
</div>