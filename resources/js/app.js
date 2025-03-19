import './bootstrap';

import Alpine from 'alpinejs';
import Dropzone from 'dropzone';
import $ from 'jquery';
import 'datatables.net-dt';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

window.Dropzone = Dropzone;
window.Alpine = Alpine;

Alpine.start();


$(document).ready(function() {
    $('#usersTable').DataTable();
});
