<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
function get_db_connection() {
    $conn = mysqli_connect("localhost", "root", "", "mini_forum");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}
?>