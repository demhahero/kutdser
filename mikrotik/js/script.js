$(document).ready(function () {
    table = $('#myTable').DataTable({
        responsive: true
    });

    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });
    $('.datepicker2').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    $('body').on("click", ".check-alert", function () {
        if (confirm('Are you sure?')) {
            return true;
        } else {
            return false;
        }
    });
});