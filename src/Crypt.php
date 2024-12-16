<?php

namespace PaymentSystem\Laravel;

use Illuminate\Encryption\Encrypter;
use PaymentSystem\Contracts\DecryptInterface;
use PaymentSystem\Contracts\EncryptInterface;

readonly class Crypt implements DecryptInterface, EncryptInterface
{
    public function __construct(
        private Encrypter $crypter,
    ) {}

    public function decrypt(string $data): string
    {
        return $this->crypter->decryptString($data);
    }

    public function encrypt(string $data): string
    {
        return $this->crypter->encryptString($data);
    }
}
