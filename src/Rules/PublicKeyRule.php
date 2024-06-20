<?php

declare(strict_types=1);
namespace Swissmakers\OpenSSH\Rules;

use Illuminate\Contracts\Validation\Rule;
use Swissmakers\OpenSSH\Exceptions\NoKeyLoadedException;
use Swissmakers\OpenSSH\PublicKey;

class PublicKeyRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        try {
            if ($value === null) {
                return false;
            }

            PublicKey::fromString($value);
        } catch (NoKeyLoadedException) {
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return 'The :attribute must be a valid public key.';
    }
}
