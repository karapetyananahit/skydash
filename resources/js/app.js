import './bootstrap';
import Alpine from 'alpinejs';
import $ from 'jquery';
import 'datatables.net-dt';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

window.Alpine = Alpine;

Alpine.start();


$(document).ready(function() {
    $('#usersTable').DataTable();
});
