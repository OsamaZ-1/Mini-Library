<?php
    $host = 'localhost';
    $user = 'assesment_z';
    $pass = 'assesment123';
    $dbname = 'assesment_z';

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>
