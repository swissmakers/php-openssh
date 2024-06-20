<?php

declare(strict_types=1);
namespace Swissmakers\OpenSSH\Tests;

use Swissmakers\OpenSSH\PrivateKey;
use Swissmakers\OpenSSH\PublicKey;

class RSAKeyTest extends TestCase
{
    protected PrivateKey $privateKey;
    protected PublicKey $publicKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->privateKey = PrivateKey::fromFile($this->getStub('id_rsa'));
        $this->publicKey = PublicKey::fromFile($this->getStub('id_rsa.pub'));
    }

    /** @test */
    public function a_public_key_can_be_used_to_encrypt_data_that_can_be_decrypted_by_a_private_key()
    {
        $originalData = 'secret data';
        $encryptedData = $this->publicKey->encrypt($originalData);

        $this->assertNotEquals($originalData, $encryptedData);

        $decryptedData = $this->privateKey->decrypt($encryptedData);

        $this->assertEquals($decryptedData, $originalData);
    }

    /** @test */
    public function it_can_sign_and_verify_a_message()
    {
        $message = 'my message';
        $signature = $this->privateKey->sign($message);

        $this->assertTrue($this->publicKey->verify($message, $signature));
        $this->assertFalse($this->publicKey->verify('my modified message', $signature));
        $this->assertFalse($this->publicKey->verify('my message', $signature.'- making the signature invalid'));
    }
}
