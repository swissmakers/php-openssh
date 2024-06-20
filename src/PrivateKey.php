<?php

declare(strict_types=1);
namespace Swissmakers\OpenSSH;

use Swissmakers\OpenSSH\Exceptions\BadDecryptionException;
use Swissmakers\OpenSSH\Exceptions\FileNotFoundException;
use Swissmakers\OpenSSH\Exceptions\NoKeyLoadedException;
use phpseclib3\Crypt\RSA;

class PrivateKey
{
    const KEY_OUTPUT_FORMAT = 'OpenSSH';

    protected function __construct(
        protected \phpseclib3\Crypt\Common\PrivateKey $key
    ) {
    }

    public static function generate(int $bits = 4096): self
    {
        return new self(RSA::createKey($bits));
    }

    /**
     * @throws NoKeyLoadedException
     */
    public static function fromString(string $keyContent): self
    {
        try {
            $key = RSA::loadPrivateKey($keyContent);
        } catch (\Throwable $exception) {
            throw new NoKeyLoadedException($exception->getMessage());
        }

        return new self($key);
    }

    /**
     * @throws NoKeyLoadedException
     * @throws FileNotFoundException
     */
    public static function fromFile(string $filename): self
    {
        if (! $keyContent = file_get_contents($filename)) {
            throw new FileNotFoundException('The file was not found: '.$filename);
        }

        return self::fromString($keyContent);
    }

    public function encrypt(string $text): string
    {
        return $this->key->getPublicKey()->encrypt($text);
    }

    public function canDecrypt(string $ciphertext): bool
    {
        try {
            $this->decrypt($ciphertext);
            return true;
        } catch (BadDecryptionException) {
            return false;
        }
    }

    /**
     * @throws BadDecryptionException
     */
    public function decrypt(string $ciphertext): string
    {
        $decrypted = $this->key->decrypt($ciphertext);

        if ($decrypted === null) {
            throw new BadDecryptionException();
        }

        return $decrypted;
    }

    public function sign(string $text): string
    {
        return $this->key->sign($text);
    }

    /**
     * @throws NoKeyLoadedException
     */
    public function getPublicKey(): PublicKey
    {
        return PublicKey::fromString($this->key->getPublicKey());
    }

    public function toFile(string $filename): void
    {
        if (file_put_contents($filename, $this->__toString()) === false) {
            throw new \RuntimeException('Failed to write key to file: ' . $filename);
        }
    }

    public function __toString(): string
    {
        return $this->key->toString(self::KEY_OUTPUT_FORMAT);
    }
}
