<?php

declare(strict_types=1);

namespace Jengo\Base\Libraries;

/**
 * Class RandomGenerator
 *
 * Provides a set of utility methods for generating various types of secure random data.
 * All methods rely on PHP's cryptographically secure random number generator.
 */
final class RandomGenerator
{
    /**
     * Generates a secure, random string consisting of a mix of characters and numbers.
     *
     * @param int $length The desired length of the random string.
     * @return string The generated random alphanumeric string.
     * @throws \Exception If a suitable random number source cannot be found.
     */
    public static function alphanumeric(int $length = 16): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charLength - 1)];
        }

        return $randomString;
    }

    /**
     * Generates a secure, random, fixed-length numeric code (e.g., for OTP or PINs).
     *
     * @param int $length The desired length of the numeric code.
     * @return string The generated random numeric string.
     * @throws \Exception If a suitable random number source cannot be found.
     */
    public static function numericCode(int $length = 6): string
    {
        $min = 10 ** ($length - 1);
        $max = (10 ** $length) - 1;

        // Ensure we handle lengths of 1 correctly (min=0, max=9)
        if ($length === 1) {
            $min = 0;
            $max = 9;
        }

        $code = (string) random_int($min, $max);

        // Pad with leading zeros if random_int returned a smaller number than expected
        // (Only possible if $min=0 and $length > 1, but safe to keep)
        return str_pad($code, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Generates a cryptographically secure random hexadecimal string.
     * Useful for nonces, salts, or unique identifiers.
     *
     * @param int $length The desired length of the hex string (must be an even number).
     * The number of random bytes used is $length / 2.
     * @return string The generated random hex string.
     * @throws \Exception If a suitable random number source cannot be found.
     */
    public static function hexString(int $length = 32): string
    {
        if ($length % 2 !== 0) {
            // Hex strings are generated from bytes, where 1 byte = 2 hex chars.
            throw new \InvalidArgumentException('Hex string length must be an even number.');
        }

        // Generate N bytes and convert them to 2N hex characters.
        $bytes = random_bytes($length / 2);

        return bin2hex($bytes);
    }

    /**
     * Generates a standard Version 4 (Random) UUID (e.g., a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11).
     *
     * @return string The generated UUID v4 string.
     * @throws \Exception If a suitable random number source cannot be found.
     */
    public static function uuidV4(): string
    {
        // 16 random bytes are needed for a 32-character hex UUID
        $data = random_bytes(16);

        // Set the version (4) and variant (RFC 4122) bits
        // Set version to 0100 (4)
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        // Set variant to 10xx (RFC 4122)
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        // Format the 32 hex characters into the standard UUID pattern (8-4-4-4-12)
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}