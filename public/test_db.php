<?php
$conn = new mysqli('localhost', 'root', '', 'koperasi_rsud');
$res = $conn->query("SELECT * FROM admin_users");
$users = [];
while($row = $res->fetch_assoc()){
    $users[] = $row;
}

if(empty($users)) {
    // create a default admin
    $pass = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO admin_users (username, password, nama_lengkap, role, status) VALUES ('admin', '$pass', 'Administrator', 'SuperAdmin', 'Aktif')");
    echo "Created default admin: admin / admin123";
} else {
    echo "Existing users:\n";
    print_r($users);
    
    // reset password of first user to admin123
    $id = $users[0]['id'];
    $pass = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("UPDATE admin_users SET password='$pass' WHERE id=$id");
    echo "\nReset password of user {$users[0]['username']} to admin123";
}

