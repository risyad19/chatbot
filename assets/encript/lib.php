<?php
/**
* Decrypt data from a CryptoJS json encoding string
*
* @param mixed $keys
* @param mixed $uniqs
* @return mixed
*/
function cryptoJsAesDecrypt($uniqs){
    $keys = "Y2hhdGJvdGFqaWNhaHlh";
    $jsondata = json_decode($uniqs, true);
    $salt = hex2bin($jsondata["s"]);
    $ct = base64_decode($jsondata["ct"]);
    $iv  = hex2bin($jsondata["iv"]);
    $concatedkeys = $keys.$salt;
    $md5 = array();
    $md5[0] = md5($concatedkeys, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1].$concatedkeys, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
}

/**
* Encrypt value to a cryptojs compatiable json encoding string
*
* @param mixed $keys
* @param mixed $value
* @return string
*/
function cryptoJsAesEncrypt($value){
    $keys = "Y2hhdGJvdGFqaWNhaHlh";
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx.$keys.$salt, true);
        $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32,16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
}

// $encrypted = cryptoJsAesEncrypt("my keys", "value to encrypt");
// $decrypted = cryptoJsAesDecrypt("my keys", $encrypted);
?>