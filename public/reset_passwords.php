<?php
$db = new mysqli('localhost', 'root', '', 'koperasi_rsud');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$hash = password_hash('123456', PASSWORD_DEFAULT);
$db->query("UPDATE users SET password_hash = '$hash';");
echo "Passwords updated for all users.\n";
$db->close();
