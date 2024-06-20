<?php

declare(strict_types=1);
namespace Swissmakers\OpenSSH\Tests;

use Illuminate\Support\Facades\Validator;
use Swissmakers\OpenSSH\PrivateKey;
use Swissmakers\OpenSSH\PublicKey;
use Swissmakers\OpenSSH\Rules\PublicKeyRule;

class PublicKeyValidationRuleTest extends TestCase
{
    /**
     * @test
     * @dataProvider providesPublicKeyToTest
     */
    public function it_should_pass_when_key_is_public(string $key): void
    {
        $validator = Validator::make(
            ['key' => PublicKey::fromFile($this->getStub($key))->__toString()],
            ['key' => new PublicKeyRule()]
        );

        $this->assertTrue($validator->passes());
    }

    public static function providesPublicKeyToTest(): \Generator
    {
        yield 'RSA public key' => [
            'id_rsa.pub',
        ];

        yield 'ed25519 public key' => [
            'id_ed25519.pub',
        ];
    }

    /** @test */
    public function it_should_not_pass_when_key_is_not_public(): void
    {
        $validator = Validator::make(
            ['key' => PrivateKey::fromFile($this->getStub('id_rsa'))->__toString()],
            ['key' => new PublicKeyRule()]
        );

        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function it_should_not_pass_when_key_is_null(): void
    {
        $validator = Validator::make(
            ['key' => null],
            ['key' => new PublicKeyRule()]
        );

        $this->assertFalse($validator->passes());
    }
}
