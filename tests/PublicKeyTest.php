<?php

declare(strict_types=1);
namespace Swissmakers\OpenSSH\Tests;

use Swissmakers\OpenSSH\Exceptions\NoKeyLoadedException;
use Swissmakers\OpenSSH\PublicKey;

class PublicKeyTest extends TestCase
{
    /** @test */
    public function it_should_throw_an_exception_if_key_is_not_valid(): void
    {
        $this->expectException(NoKeyLoadedException::class);
        PublicKey::fromString('invalid-key');
    }

    /**
     * @test
     * @dataProvider providesPublicKeyToTest
     */
    public function it_should_load_a_public_key_from_a_string(string $filename): void
    {
        $keyContent = file_get_contents($this->getStub($filename));
        $key = PublicKey::fromString($keyContent);

        $this->assertInstanceOf(PublicKey::class, $key);
    }

    public function providesPublicKeyToTest(): \Generator
    {
        yield 'RSA public key' => [
            'id_rsa.pub',
            [
                'sha256' => '/FIGE87Mc1T9//aom+2nfF2y/KgC2S0KngKeLGT1+R4',
                'md5' => 'dc:9d:b4:56:14:08:5b:08:70:cc:e8:86:4c:e3:0d:16',
            ]
        ];

        yield 'ed25519 public key' => [
            'id_ed25519.pub',
            [
                'sha256' => 'aBO/q1Wb7Z3+nWqPnsqUl0cw+BsTmu5IZMa4A3/dxA0',
                'md5' => '28:b1:af:e2:a7:ef:71:c9:f2:b0:e5:7f:3d:a0:6d:bc',
            ]
        ];
    }

    /**
     * @test
     * @dataProvider providesPublicKeyToTest
     */
    public function it_should_load_a_public_key_from_a_file(string $filename): void
    {
        $key = PublicKey::fromFile($this->getStub($filename));

        $this->assertInstanceOf(PublicKey::class, $key);
    }

    /**
     * @test
     * @dataProvider providesPublicKeyToTest
     */
    public function it_should_encrypt_a_text_using_RSA_public_key(): void
    {
        $key = PublicKey::fromFile($this->getStub('id_rsa.pub'));

        $this->assertNotEquals('foo bar swissmakers', $key->encrypt('foo bar swissmakers'));
    }

    /**
     * @test
     * @dataProvider providesPublicKeyToTest
     */
    public function it_should_get_the_sha256_fingerprint_of_a_key(string $filename, array $fingerprints): void
    {
        $key = PublicKey::fromFile($this->getStub($filename));

        $this->assertEquals($fingerprints['sha256'], $key->getFingerPrint('sha256'));
    }

    /**
     * @test
     * @dataProvider providesPublicKeyToTest
     */
    public function it_should_get_the_md5_fingerprint_of_a_key(string $filename, array $fingerprints): void
    {
        $key = PublicKey::fromFile($this->getStub($filename));

        $this->assertEquals($fingerprints['md5'], $key->getFingerPrint('md5'));
    }
}
