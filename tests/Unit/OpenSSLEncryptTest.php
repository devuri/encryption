<?php

namespace Tests\Unit\App\Console;

use PHPUnit\Framework\TestCase;
use Urisoft\OpenSSLEncrypt;

/**
 * @internal
 *
 * @coversNothing
 */
class OpenSSLEncryptTest extends TestCase
{
    protected string $testKey;
    protected string $secretTestData;

    protected function setUp(): void
    {
        $this->testKey        = 'my-secure-test-key-1234';
        $this->secretTestData = 'This is our secret test string';
    }

    public function test_encrypt_and_decrypt_base64(): void
    {
        $encryptor = new OpenSSLEncrypt( $this->testKey );

        $encrypted = $encryptor->encrypt( $this->secretTestData );

        $this->assertNotEmpty( $encrypted );
        $this->assertNotEquals( $this->secretTestData, $encrypted );

        $decrypted = $encryptor->decrypt( $encrypted );

        $this->assertEquals( $this->secretTestData, $decrypted );
    }

    public function test_encrypt_and_decrypt_hex(): void
    {
        $encryptor = new OpenSSLEncrypt( $this->testKey );

        $encrypted = $encryptor->encrypt( $this->secretTestData, 'hex' );

        $this->assertNotEmpty( $encrypted );
        $this->assertTrue( ctype_xdigit( $encrypted ), 'Encrypted output should be valid hex' );

        $decrypted = $encryptor->decrypt( $encrypted, 'hex' );

        $this->assertEquals( $this->secretTestData, $decrypted );
    }

    public function test_encrypt_and_decrypt_binary(): void
    {
        $encryptor = new OpenSSLEncrypt( $this->testKey );

        $encrypted = $encryptor->encrypt( $this->secretTestData, 'binary' );

        $this->assertNotEmpty( $encrypted );

        $decrypted = $encryptor->decrypt( $encrypted, 'binary' );

        $this->assertEquals( $this->secretTestData, $decrypted );
    }

    public function test_encrypt_with_different_cipher(): void
    {
        $encryptor = new OpenSSLEncrypt( $this->testKey, 'AES-256-CBC' );

        $encrypted = $encryptor->encrypt( $this->secretTestData );

        $this->assertNotEmpty( $encrypted );

        $decrypted = $encryptor->decrypt( $encrypted );

        $this->assertEquals( $this->secretTestData, $decrypted );
    }

    public function test_invalid_cipher_throws_exception(): void
    {
        $this->expectException( \InvalidArgumentException::class );
        $this->expectExceptionMessage( 'Unsupported cipher algorithm.' );

        new OpenSSLEncrypt( $this->testKey, 'INVALID-CIPHER' );
    }

    public function test_invalid_base64_input_throws_exception(): void
    {
        $encryptor = new OpenSSLEncrypt( $this->testKey );

        $this->expectException( \RuntimeException::class );
        $this->expectExceptionMessage( 'Invalid base64 input.' );

        $encryptor->decrypt( '!!!invalid-base64!!!', 'base64' );
    }

    public function test_invalid_hex_input_throws_exception(): void
    {
        $encryptor = new OpenSSLEncrypt( $this->testKey );

        $this->expectException( \RuntimeException::class );
        $this->expectExceptionMessage( 'Invalid hex input.' );

        $encryptor->decrypt( 'not-valid-hex!@#', 'hex' );
    }

    public function test_different_keys_cannot_decrypt(): void
    {
        $encryptor1 = new OpenSSLEncrypt( 'key-one-secret' );
        $encryptor2 = new OpenSSLEncrypt( 'key-two-secret' );

        $encrypted = $encryptor1->encrypt( $this->secretTestData );

        // Decryption with wrong key should fail or return garbage
        $this->expectException( \RuntimeException::class );

        $encryptor2->decrypt( $encrypted );
    }

    public function test_encrypt_produces_different_output_each_time(): void
    {
        $encryptor = new OpenSSLEncrypt( $this->testKey );

        $encrypted1 = $encryptor->encrypt( $this->secretTestData );
        $encrypted2 = $encryptor->encrypt( $this->secretTestData );

        // Due to random IV, same plaintext should produce different ciphertext
        $this->assertNotEquals( $encrypted1, $encrypted2 );

        // But both should decrypt to the same value
        $this->assertEquals( $this->secretTestData, $encryptor->decrypt( $encrypted1 ) );
        $this->assertEquals( $this->secretTestData, $encryptor->decrypt( $encrypted2 ) );
    }
}
