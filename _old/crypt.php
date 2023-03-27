<?php

/**
 * 
 */
function ssl_crypt(string $data): string|false
{
    $encrypt = openssl_encrypt(
        data: $data,
        cipher_algo: 'AES-128-CBC',
        passphrase: pack('a16', $_ENV['OPENSSL_SECRET']),
        options: 0,
        iv: pack('a16', $_ENV['OPENSSL_SECRET'])
    );

    return base64_encode($encrypt);
}

/**
 * 
 */
function ssl_decrypt(string $data): string|false
{
    return openssl_decrypt(
        data: base64_decode($data),
        cipher_algo: 'AES-128-CBC',
        passphrase: pack('a16', $_ENV['OPENSSL_SECRET']),
        options: 0,
        iv: pack('a16', $_ENV['OPENSSL_SECRET'])
    );
}
