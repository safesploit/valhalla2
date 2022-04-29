<?php

public function resetPassword($email)
{
    $token = hash('sha256',date("Y-m-d H:i:s"));
    //$email = row['email'];
    //$valid_from = date("Y-m-d H:i:s");
    $expired = date('Y-m-d H:i:s', strtotime("+1 hour"));
    $url = 'https://valhalla.sws-internal/password_reset.php?token=';

    // (id, email, is_token_expired, is_token_used)
    //$query = mysqli($conn, "INSERT INTO reset_password VALUES(NULL, '$token', '$email', '$expired', 'no'));
    $reset_url = $url . $token;
    return $reset_url;
}

?>