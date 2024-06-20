<?php

declare(strict_types=1);
namespace Swissmakers\OpenSSH\Tests;

use Illuminate\Support\Facades\Validator;
use Swissmakers\OpenSSH\PrivateKey;
use Swissmakers\OpenSSH\PublicKey;
use Swissmakers\OpenSSH\Rules\PrivateKeyRule;

class PrivateKeyValidationRuleTest extends TestCase
{
    /** @test */
    public function it_should_pass_when_key_is_private(): void
    {
        $validator = Validator::make(
            ['key' => PrivateKey::fromFile($this->getStub('id_rsa'))->__toString()],
            ['key' => new PrivateKeyRule()]
        );

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_should_not_pass_when_key_is_not_private(): void
    {
        $validator = Validator::make(
            ['key' => PublicKey::fromFile($this->getStub('id_rsa.pub'))->__toString()],
            ['key' => new PrivateKeyRule()]
        );

        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function it_should_not_pass_when_key_is_null(): void
    {
        $validator = Validator::make(
            ['key' => null],
            ['key' => new PrivateKeyRule()]
        );

        $this->assertFalse($validator->passes());
    }
}
