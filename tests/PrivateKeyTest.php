<?php

declare(strict_types=1);
namespace Swissmakers\OpenSSH\Tests;

use Swissmakers\OpenSSH\Exceptions\NoKeyLoadedException;
use Swissmakers\OpenSSH\PrivateKey;
use Swissmakers\OpenSSH\PublicKey;

class PrivateKeyTest extends TestCase
{
    /** @test */
    public function it_should_throw_an_exception_if_key_is_not_valid(): void
    {
        $this->expectException(NoKeyLoadedException::class);

        PrivateKey::fromString('invalid-key');
    }

    /** @test */
    public function it_should_load_a_private_key_from_a_string(): void
    {
        $keyContent = file_get_contents($this->getStub('id_rsa'));
        $key = PrivateKey::fromString($keyContent);

        $this->assertInstanceOf(PrivateKey::class, $key);
    }

    /** @test */
    public function it_should_load_a_private_key_from_a_file(): void
    {
        $key = PrivateKey::fromFile($this->getStub('id_rsa'));

        $this->assertInstanceOf(PrivateKey::class, $key);
    }

    /** @test */
    public function it_should_encrypt_and_decrypt_a_text(): void
    {
        $key = PrivateKey::fromFile($this->getStub('id_rsa'));

        $ciphertext = $key->encrypt('foo bar swissmakers');

        $this->assertTrue($key->canDecrypt($ciphertext));
        $this->assertEquals('foo bar swissmakers', $key->decrypt($ciphertext));
    }

    /** @test */
    public function it_should_return_the_associated_public_key(): void
    {
        $key = PrivateKey::fromFile($this->getStub('id_rsa'));

        $this->assertInstanceOf(PublicKey::class, $key->getPublicKey());
    }

    /** @test */
    public function it_should_write_the_key_to_a_file(): void
    {
        $filename = $this->getTempPath('testing_an_OpenSSH_key');

        // Save a private key into the disk
        $originalKey = PrivateKey::generate();
        $originalKey->toFile($filename);

        // Read the previous saved key
        $savedKey = PrivateKey::fromFile($filename);

        // Check that it is the same key that was saved
        $originalText = 'foo bar swissmakers';
        $cipherText = $originalKey->encrypt($originalText);
        $this->assertEquals($originalText, $savedKey->decrypt($cipherText));
    }

    /** @test */
    public function it_should_return_a_string_with_the_content_of_the_key(): void
    {
        $privateKey = PrivateKey::fromFile($this->getStub('id_rsa'));

        $this->assertStringStartsWith('-----BEGIN OPENSSH PRIVATE KEY-----', (string)$privateKey);
    }
}
