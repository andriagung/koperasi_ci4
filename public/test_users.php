<?php
$db = new mysqli('localhost', 'root', '', 'koperasi_rsud');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$result = $db->query("SELECT * FROM users;");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "Username: " . $row['username'] . " | Role ID: " . $row['role_id'] . "\n";
    }
}
$result = $db->query("SELECT * FROM roles;");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "Role ID: " . $row['id'] . " | Role Name: " . ($row['role_name'] ?? $row['name'] ?? 'N/A') . "\n";
    }
}
$db->close();
