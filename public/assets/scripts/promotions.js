document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('promotionForm');
    const tokenInput = form?.querySelector('input[name="_token"]');
    const viewBtn = document.getElementById('viewStudents');
    const promoteBtn = document.getElementById('promote');

    if (form && tokenInput) {
        viewBtn?.addEventListener('click', () => {
            form.method = 'get';
            tokenInput.disabled = true;
        });

        promoteBtn?.addEventListener('click', () => {
            form.method = 'post';
            tokenInput.disabled = false;
        });
    }

    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = Array.from(
        document.querySelectorAll('.unenrolled-checkbox, input[name="student_ids[]"]')
    );

    if (!selectAll) return;

    selectAll.disabled = rowCheckboxes.length === 0;

    selectAll.addEventListener('change', () => {
        rowCheckboxes.forEach((checkbox) => {
            if (!checkbox.disabled) {
                checkbox.checked = selectAll.checked;
            }
        });
    });

    rowCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            const enabled = rowCheckboxes.filter((cb) => !cb.disabled);
            const allChecked = enabled.length > 0 && enabled.every((cb) => cb.checked);
            const someChecked = enabled.some((cb) => cb.checked);

            selectAll.checked = allChecked;
            selectAll.indeterminate = !allChecked && someChecked;
        });
    });
});