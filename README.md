# php-openssh Library

This library allows you to generate 4096-bit OpenSSH private/public key pairs, which can be used for SSH authentication and to encrypt/decrypt strings.

```php
use Swissmakers\OpenSSH\PrivateKey;
use Swissmakers\OpenSSH\PublicKey;

// Generate a new OpenSSH key pair
$privateKey = PrivateKey::generate();
$publicKey = $privateKey->getPublicKey();

// Encrypt and decrypt data using the key pair
$data = 'my secret data';
$encryptedData = $publicKey->encrypt($data); // Returns unreadable data
$decryptedData = $privateKey->decrypt($encryptedData); // Returns 'my secret data' in plaintext
```

## Installation

Install the package via Composer:

```bash
composer require swissmakers/php-openssh
```

## Usage

Generate a private key and save it to a file:

```php
use Swissmakers\OpenSSH\PrivateKey;

$privateKey = PrivateKey::generate();
$privateKey->toFile('/home/foo/bar');
```

### Loading Keys

Load a key from a file:

```php
use Swissmakers\OpenSSH\PrivateKey;
use Swissmakers\OpenSSH\PublicKey;

$privateKey = PrivateKey::fromFile($pathToPrivateKey);
$publicKey = PublicKey::fromFile($pathToPublicKey);
```

Load a key from a string:

```php
use Swissmakers\OpenSSH\PrivateKey;
use Swissmakers\OpenSSH\PublicKey;

$privateKey = PrivateKey::fromString($privateKeyContent);
$publicKey = PublicKey::fromString($publicKeyContent);
```

Obtain the public key from a private key:

```php
use Swissmakers\OpenSSH\PrivateKey;

$privateKey = PrivateKey::fromString($privateKeyContent);
$publicKey = $privateKey->getPublicKey();
```

### Encrypting and Decrypting Data

Encrypt data with a public key and decrypt it with the private key:

```php
use Swissmakers\OpenSSH\PrivateKey;
use Swissmakers\OpenSSH\PublicKey;

$data = 'my secret data';

$publicKey = PublicKey::fromFile($pathToPublicKey);
$encryptedData = $publicKey->encrypt($data);

$privateKey = PrivateKey::fromFile($pathToPrivateKey);
$decryptedData = $privateKey->decrypt($encryptedData); // Returns 'my secret data'
```

If decryption fails, a `\Swissmakers\OpenSSH\Exceptions\BadDecryptionException` will be thrown.

### Checking Decryption Capability

Determine if data can be decrypted with the private key:

```php
use Swissmakers\OpenSSH\PrivateKey;

$privateKey = PrivateKey::fromFile($pathToPrivateKey);
$canDecrypt = $privateKey->canDecrypt($data); // Returns a boolean
```

### Signing and Verifying Data

Sign data with a private key and verify it with a public key:

```php
use Swissmakers\OpenSSH\PrivateKey;
use Swissmakers\OpenSSH\PublicKey;

$privateKey = PrivateKey::fromFile($pathToPrivateKey);
$signature = $privateKey->sign('my message'); // Returns a string

$publicKey = PublicKey::fromFile($pathToPublicKey);
$isVerified = $publicKey->verify('my message', $signature); // Returns true
$isModifiedVerified = $publicKey->verify('my modified message', $signature); // Returns false
```

### Validating Inputs (Laravel)

Validate form inputs to check for valid public or private keys:

```php
use Swissmakers\OpenSSH\Rules\PublicKeyRule;
use Swissmakers\OpenSSH\Rules\PrivateKeyRule;

public function rules(): array
{
    return [
        'public_key' => [
            new PublicKeyRule(),
        ],
        'private_key' => [
            new PrivateKeyRule(),
        ],
    ];
}
```

## Testing

Run tests using:

```bash
composer test
```

## License

The GNU General Public License v3.0. See the [License File](LICENSE.md) for more information.
