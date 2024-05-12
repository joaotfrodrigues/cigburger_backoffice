<?php

if (!function_exists('Encrypt')) {
    /**
     * Encrypts a value.
     *
     * @param string $value The value to encrypt.
     * @return string|null The encrypted value in hexadecimal format, or null if encryption fails.
     */
    function Encrypt($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            $encryption = \Config\Services::encrypter();
            return bin2hex($encryption->encrypt($value));
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('Decrypt')) {
    /**
     * Decrypts a value.
     *
     * @param string $value The value to decrypt in hexadecimal format.
     * @return string|null The decrypted value, or null if decryption fails.
     */
    function Decrypt($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            $encryption = \Config\Services::encrypter();
            return $encryption->decrypt(hex2bin($value));
        } catch (\Exception $e) {
            return null;
        }
    }
}
