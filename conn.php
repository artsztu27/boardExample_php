<?php
  $server_name = 'localhost';
  $username = 'shin';
  $password = 'shin';
  $db_name = 'shin';

  $conn = new mysqli($server_name, $username, $password, $db_name);

  if ($conn->connect_error) {
    echo 'database connect error';
    die('database connect error:' . $conn->connect_error);
  }

  $conn->query('SET NAMES UTF8');
  $conn->query('SET time_zone = "+1:00"');
?>