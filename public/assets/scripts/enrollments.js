document.addEventListener('DOMContentLoaded',function(){
    const selectAllCheckBox=document.getElementById('selectAll');
    const studentCheckBoxes=document.querySelectorAll('.student-checkbox');

    if (selectAllCheckBox) {
        selectAllCheckBox.addEventListener('change',function(){
            studentCheckBoxes.forEach(checkbox=>{
                checkbox.checked=selectAllCheckBox.checked;
            });
        });
    }
const form=document.querySelector('.form form');
if (form) {
    form.id='enrollmentForm';
    studentCheckBoxes.forEach(cb=>cb.setAttribute('form','enrollmentForm'));
}

});