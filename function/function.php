<?php

/**
 * Update the User
 * 
 * @param int $id
 * 
 * @return array
 */
function update_user($id)
{
    global $conn;
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Remove password
    unset($user['password']);

    return $user;
}


/**
 * Get user name by id
 * 
 * @param int $id
 * 
 * @return string
 */
function get_username_by_id($id)
{
    global $conn;
    $sql = "SELECT username FROM users WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['username'];
}

/**
 * Check use is admin
 * 
 * @param int $id
 * 
 * @return array
 */
function check_user_is_ban($id)
{
    global $conn;
    $sql_ban = "SELECT * FROM ban WHERE user = ? AND is_ban = 1 AND time_end > NOW()";
    $stmt = $conn->prepare($sql_ban);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ban = $result->fetch_assoc();

    return $ban;
}

/**
 * Unban user
 * 
 * @param int $id
 * 
 * @return void
 */
function unban_user($id)
{
    global $conn;
    $sql_unban = "UPDATE ban SET is_ban = 0 WHERE id = ?";
    $stmt_unban = $conn->prepare($sql_unban);
    $stmt_unban->bind_param("i", $id);
    $stmt_unban->execute();
}

/**
 * Update user IP
 * 
 * @param int $id
 * @param string $ip
 * 
 * @return void
 */
function update_user_ip($id)
{
    global $conn;

    // Add IP to user
    $user_ip = $_SERVER['REMOTE_ADDR']; // Lấy địa chỉ IP của người dùng


    // Kiểm tra nếu địa chỉ IP là từ phía sau proxy
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Nếu có, lấy địa chỉ IP thực sự của người dùng (thường là IP của proxy)
        $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    // Update IP
    $sql_update_ip = "UPDATE users SET ip = ? WHERE id = ?";
    $stmt_update_ip = $conn->prepare($sql_update_ip);
    $stmt_update_ip->bind_param("si", $user_ip, $id);
    $stmt_update_ip->execute();
}

/**
 * Get home_url
 * 
 * @return string
 */
function home_url()
{
    global $conn;
    $sql_config = "SELECT * FROM config WHERE `key` = 'home_url'";
    $result_config = mysqli_query($conn, $sql_config);
    $config = mysqli_fetch_assoc($result_config);

    return $config['value'];
}
