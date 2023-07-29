# Encryption Package

The **Encryption** package is a simple Composer package that provides functionality for encrypting and decrypting data using the [Defuse PHP encryption library](https://github.com/defuse/php-encryption). This package simplifies the process of handling encryption keys and allows you to perform encryption and decryption operations on files and data with ease.

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
use Symfony\Component\Filesystem\Filesystem;

// Replace these with the appropriate values for your application
$rootDirPath = __DIR__;
$filesystem = new Filesystem();
$secretKeyPath = '/path/to/secret_key_directory';
$keyId = 'secret'; // Optional, default is 'secret'

try {
    $encryption = new Encryption($rootDirPath, $filesystem, $secretKeyPath, $keyId);
} catch (InvalidArgumentException $e) {
    // Handle exception if the secret key file is not found
}
```

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

## License

This package is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).

## Acknowledgments

The Encryption package utilizes the [Defuse PHP encryption library](https://github.com/defuse/php-encryption) for encryption and decryption operations.

For more information on how to use the Defuse PHP encryption library, please refer to its documentation.

**Note**: Replace the ellipsis (...) in the usage examples with actual data or file paths relevant to your application. Always handle exceptions appropriately when performing encryption and decryption operations.
