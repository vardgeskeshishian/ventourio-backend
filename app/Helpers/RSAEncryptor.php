<?php

namespace App\Helpers;

use Exception;

final class RSAEncryptor
{
    /**
     * @throws Exception
     */
    public static function encrypt($input): string
    {
        $publicKey = file_get_contents(config('goglobal.auth.dev.pub_key'));
        if ( ! $publicKey) {
            throw new Exception(__('errors.system.rsa_encryption_failed'));
        }

        $success = openssl_public_encrypt($input, $encrypted, $publicKey, OPENSSL_PKCS1_OAEP_PADDING);
        if ( ! $success) {
            throw new Exception(__('errors.system.rsa_encryption_failed'));
        }

        return base64_encode($encrypted);
    }
}
