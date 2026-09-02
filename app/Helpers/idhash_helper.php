<?php

if (!function_exists('idhash_encode')) {
    /**
     * Encode integer ID into an obfuscated string
     *
     * @param int|string $id
     * @return string
     */
    function idhash_encode($id)
    {
        if (empty($id)) return '';
        return \Config\Services::idhash()->encode((int) $id);
    }
}

if (!function_exists('idhash_decode')) {
    /**
     * Decode obfuscated string back to integer ID
     *
     * @param string $hash
     * @return int|null
     */
    function idhash_decode($hash)
    {
        if (empty($hash)) return null;
        return \Config\Services::idhash()->decode($hash);
    }
}
