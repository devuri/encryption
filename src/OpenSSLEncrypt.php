<?php

namespace Urisoft;

class OpenSSLEncrypt
{
    private string $encryptionKey;
    private string $cipherAlgorithm;
    private array $allowedCiphers;

    /**
     * Constructor for OpenSSLEncrypt.
     *
     * @param string $key    The encryption key.
     * @param string $cipher The cipher algorithm.
     *
     * @throws \InvalidArgumentException If an unsupported cipher is provided.
     */
    public function __construct( string $key, string $cipher = 'AES-128-CBC' )
    {
        $this->allowedCiphers = openssl_get_cipher_methods();
        $this->set_cipher_algorithm( $cipher );
        $this->encryptionKey = $key;
    }

    /**
     * Sets the cipher algorithm.
     *
     * @param string $cipher The cipher algorithm.
     *
     * @return void
     *
     * @throws \InvalidArgumentException If an unsupported cipher is provided.
     */
    private function set_cipher_algorithm( string $cipher ): void
    {
        // OpenSSL cipher names are case-insensitive, normalize to lowercase for comparison.
        $cipherLower   = strtolower( $cipher );
        $allowedLower  = array_map( 'strtolower', $this->allowedCiphers );

        if ( ! in_array( $cipherLower, $allowedLower, true ) ) {
            throw new \InvalidArgumentException( 'Unsupported cipher algorithm.' );
        }

        // Use lowercase version for consistency with openssl functions.
        $this->cipherAlgorithm = $cipherLower;
    }

    /**
     * Encrypts the given data.
     *
     * @param string $data         The data to encrypt.
     * @param string $outputFormat The output format (base64, hex, binary).
     *
     * @return string The encrypted data.
     *
     * @throws \RuntimeException If encryption fails.
     */
    public function encrypt( string $data, string $outputFormat = 'base64' ): string
    {
        $ivLength      = openssl_cipher_iv_length( $this->cipherAlgorithm );
        $iv            = openssl_random_pseudo_bytes( $ivLength );
        $encryptedData = openssl_encrypt( $data, $this->cipherAlgorithm, $this->encryptionKey, OPENSSL_RAW_DATA, $iv );

        if ( false === $encryptedData ) {
            throw new \RuntimeException( 'Encryption failed.' );
        }

        $encryptedData = $iv . $encryptedData;

        return $this->convert_output( $encryptedData, $outputFormat );
    }

    /**
     * Decrypts the given data.
     *
     * @param string $data        The data to decrypt.
     * @param string $inputFormat The input format (base64, hex, binary).
     *
     * @return string The decrypted data.
     *
     * @throws \RuntimeException If decryption fails or input format is invalid.
     */
    public function decrypt( string $data, string $inputFormat = 'base64' ): string
    {
        $data     = $this->convert_input( $data, $inputFormat );
        $ivLength = openssl_cipher_iv_length( $this->cipherAlgorithm );
        $iv       = substr( $data, 0, $ivLength );
        $data     = substr( $data, $ivLength );

        $decryptedData = openssl_decrypt( $data, $this->cipherAlgorithm, $this->encryptionKey, OPENSSL_RAW_DATA, $iv );

        if ( false === $decryptedData ) {
            throw new \RuntimeException( 'Decryption failed.' );
        }

        return $decryptedData;
    }

    /**
     * Converts output data to the specified format.
     *
     * @param string $data   The data to convert.
     * @param string $format The output format.
     *
     * @return string The converted data.
     */
    private function convert_output( string $data, string $format ): string
    {
        switch ( $format ) {
            case 'base64':
                return base64_encode( $data );
            case 'hex':
                return bin2hex( $data );
            default:
                return $data;
        }
    }

    /**
     * Converts input data from the specified format.
     *
     * @param string $data   The data to convert.
     * @param string $format The input format.
     *
     * @return string The converted data.
     *
     * @throws \RuntimeException If the input data is invalid for the specified format.
     */
    private function convert_input( string $data, string $format ): string
    {
        switch ( $format ) {
            case 'base64':
                $decoded = base64_decode( $data, true );
                if ( false === $decoded ) {
                    throw new \RuntimeException( 'Invalid base64 input.' );
                }
                return $decoded;
            case 'hex':
                if ( ! ctype_xdigit( $data ) || 0 !== strlen( $data ) % 2 ) {
                    throw new \RuntimeException( 'Invalid hex input.' );
                }
                return hex2bin( $data );
            default:
                return $data;
        }
    }
}
