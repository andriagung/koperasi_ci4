<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('encrypt_id')) {
    function encrypt_id($id) {
        if (empty($id)) return '';
        $cipher = "AES-128-CTR";
        $key = "KoperasiAssyifa123!"; // Simple salt key
        $iv = "1234567890123456"; // 16 bytes IV
        return bin2hex(openssl_encrypt((string)$id, $cipher, $key, 0, $iv));
    }
}

if (!function_exists('decrypt_id')) {
    function decrypt_id($encrypted_id) {
        if (empty($encrypted_id)) return null;
        $cipher = "AES-128-CTR";
        $key = "KoperasiAssyifa123!";
        $iv = "1234567890123456";
        try {
            $hexDecoded = @hex2bin($encrypted_id);
            if ($hexDecoded === false) return null;
            $decrypted = openssl_decrypt($hexDecoded, $cipher, $key, 0, $iv);
            return is_numeric($decrypted) ? (int)$decrypted : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
