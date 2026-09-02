<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/admin/ajax-anggota';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_POST['draw'] = 1;
$_POST['start'] = 0;
$_POST['length'] = 10;
$_POST['search'] = ['value' => ''];
$_POST['order'] = [['column' => 0, 'dir' => 'asc']];
$_POST['columns'] = [
    ['data' => 'nip', 'searchable' => 'true', 'orderable' => 'true'],
    ['data' => 'nama_lengkap', 'searchable' => 'true', 'orderable' => 'true'],
];

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'Boot.php';

CodeIgniter\Boot::bootWeb($paths);
