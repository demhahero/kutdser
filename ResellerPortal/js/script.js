$(document).ready(function () {
    $('#myTable').DataTable({
        responsive: true
    });
    
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });
});