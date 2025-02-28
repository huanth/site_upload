<?php
// Logout
session_start();
session_destroy();

// Redirect to login
header('Location: /login');
