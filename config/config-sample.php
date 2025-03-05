<?php
// Config database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'upload';
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}


// Load config
$sql_config = "SELECT * FROM config WHERE `key_data` = 'home_url'";
$result_config = mysqli_query($conn, $sql_config);
$config = mysqli_fetch_assoc($result_config);

// Config
$home_url = $config['value_data'];
