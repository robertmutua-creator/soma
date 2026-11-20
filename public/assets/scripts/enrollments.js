document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('enrollmentForm');
    const enrollBtn = document.getElementById('enroll');
    const viewBtn = document.getElementById('view');
    const tokenInput = document.getElementById('token');
    const selectAll = document.getElementById('selectAllUnenrolled');
    const boxes = document.querySelectorAll('.unenrolled-checkbox');

    if (!form) return;

    enrollBtn?.addEventListener('click', () => {
        if (tokenInput) tokenInput.disabled = false;
        form.action = '/school/enrollments/store';
        form.method = 'POST';
    });

    viewBtn?.addEventListener('click', () => {
        if (tokenInput) tokenInput.disabled = true;
        form.action = '/school/enrollments';
        form.method = 'GET';
    });

    selectAll?.addEventListener('change', () => {
        boxes.forEach((cb) => {
            cb.checked = selectAll.checked;
        });
    });

    document.addEventListener('change', (e) => {
        if (e.target.matches('.unenrolled-checkbox') && !e.target.checked && selectAll) {
            selectAll.checked = false;
        }
    });
});