# Encryption Package

The **Encryption** package is a simple Composer package that provides functionality for encrypting and decrypting data using the [Defuse PHP encryption library](https://github.com/defuse/php-encryption) or encrypting and decrypting data using OpenSSL. This package simplifies the process of handling encryption keys and allows you to perform encryption and decryption operations on files and data with ease.

## Requirements

- PHP 7.4 or higher.
- OpenSSL PHP Extension enabled.

## Installation

To use this package in your PHP application, you need to have [Composer](https://getcomposer.org/) installed. Once you have Composer set up, run the following command to add the package to your project:

```bash
composer require devuri/encryption
```

## Usage

### Encryption Key Generation

To encrypt and decrypt data using the library, you need to generate an encryption key. The `EncryptionKey` class provides a simple way to generate a new random encryption key in ASCII format:

```php
use Urisoft\EncryptionKey;

// Generate a new encryption key
$key = EncryptionKey::generate_key();

// Store the generated key securely (e.g., in a secret key file) for future use.
```

Ensure you keep the generated encryption key secure and protected, as it is essential for encrypting and decrypting sensitive data.

### Setting up Encryption

To use the Encryption package, you first need to set up the `Encryption` class by providing the necessary parameters:

```php
use Urisoft\Encryption;
use Urisoft\Filesystem;

// Replace these with the appropriate values for your application
$rootDirPath = __DIR__;
$filesystem = new Filesystem(); // Optional, required only for encrypt_envfile()
$secretKeyPath = '/path/to/secret_key_directory';
$keyId = 'secret'; // Optional, default is 'secret'

try {
    $encryption = new Encryption($rootDirPath, $filesystem, $secretKeyPath, $keyId);
} catch (InvalidArgumentException $e) {
    // Handle exception if the secret key file is not found
}
```

> **Note**: The `$filesystem` parameter is optional. Pass `null` if you don't need the `encrypt_envfile()` method. You can also provide your own implementation of `FilesystemInterface`.

### Encrypting and Decrypting Files

You can use the `encrypt_file()` and `decrypt_file()` methods to encrypt and decrypt files:

```php
try {
    // Encrypt a file
    $inputFile = '/path/to/input_file.txt';
    $outputFile = '/path/to/encrypted_file.txt';
    $encryption->encrypt_file($inputFile, $outputFile);

    // Decrypt the encrypted file
    $encryptedFile = '/path/to/encrypted_file.txt';
    $decryptedFile = '/path/to/decrypted_file.txt';
    $encryption->decrypt_file($encryptedFile, $decryptedFile);
} catch (Exception $e) {
    // Handle encryption/decryption errors
}
```

### Encrypting Data

You can use the `encrypt()` method to encrypt data, such as sensitive information:

```php
$dataToEncrypt = 'This is sensitive data';
$encryptedData = $encryption->encrypt($dataToEncrypt);

// Optionally, encode the encrypted data in base64
$base64EncodedData = $encryption->encrypt($dataToEncrypt, true);
```

### Decrypting Data

To decrypt encrypted data, use the `decrypt()` method:

```php
$encryptedData = '...'; // Replace this with the actual encrypted data
$decryptedData = $encryption->decrypt($encryptedData);

// Optionally, if the encrypted data was base64-encoded
$base64EncodedData = '...'; // Replace this with the actual base64-encoded data
$decodedDecryptedData = $encryption->decrypt($base64EncodedData, true);

if ($decryptedData === null || $decodedDecryptedData === null) {
    // Decryption failed or wrong key was loaded
    // Handle the error accordingly
}
```

### Encrypting .env File

The package provides a method to encrypt the contents of a .env file:

```php
try {
    // Encrypt the .env file
    $encryption->encrypt_envfile('/.env');
} catch (Exception $e) {
    // Handle encryption errors
}
```

The encrypted contents will be saved in a file named `.env.encrypted`.

## Key Management

The Encryption class expects the secret key file to be stored in ASCII format. It retrieves the encryption key from the secret key file specified during initialization. Make sure to keep the secret key file secure and protected.

If you have a constant `WEBAPP_ENCRYPTION_KEY` defined, the class will use that as the encryption key. Otherwise, it will look for the secret key file in the provided directory with the default filename identifier 'secret'. The key file should be named as '.secret.txt' by default unless you specify a different key filename identifier during initialization.


# OpenSSLEncrypt

The `OpenSSLEncrypt` class, provides a simple interface for encrypting and decrypting data using OpenSSL. It supports various cipher algorithms and output formats, such as base64 and hex.

## Usage

```php
use Urisoft\OpenSSLEncrypt;

$key = 'your-encryption-key'; // Replace with your secure key.
$cipher = 'AES-128-CBC'; // Optional, default is AES-128-CBC.

$encryptor = new OpenSSLEncrypt($key, $cipher);
```

If you don't specify a cipher, AES-128-CBC is used by default.

### Encrypting Data

```php
$plainText = "Hello, World!";
$outputFormat = 'base64'; // Optional, formats: base64, hex, binary. Default is base64.

$encryptedText = $encryptor->encrypt($plainText, $outputFormat);
```

### Decrypting Data

```php
$inputFormat = 'base64'; // Should match the format used in encryption. Default is base64.

$decryptedText = $encryptor->decrypt($encryptedText, $inputFormat);
```

## Methods

- `__construct(string $key, string $cipher = 'AES-128-CBC')`: Constructor to initialize the encryption key and cipher algorithm. Throws `InvalidArgumentException` if an unsupported cipher is provided.
- `encrypt(string $data, string $outputFormat = 'base64')`: Encrypts the provided data and returns it in the specified format. Throws `RuntimeException` if encryption fails.
- `decrypt(string $data, string $inputFormat = 'base64')`: Decrypts the provided data assuming it's in the specified format. Throws `RuntimeException` if decryption fails or input format is invalid.

## Notes

- It is crucial to use a secure and unique key for encryption.
- Cipher names are case-insensitive (e.g., `AES-128-CBC` and `aes-128-cbc` are both valid).
- The choice of cipher algorithm can be modified based on your requirements. Use `openssl_get_cipher_methods()` to list available ciphers.
- The class automatically handles format conversions for input and output.

## License

This package is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).

## Acknowledgments

The Encryption package utilizes the [Defuse PHP encryption library](https://github.com/defuse/php-encryption) for encryption and decryption operations.

For more information on how to use the Defuse PHP encryption library, please refer to its documentation.

**Note**: Replace the ellipsis (...) in the usage examples with actual data or file paths relevant to your application. Always handle exceptions appropriately when performing encryption and decryption operations.
