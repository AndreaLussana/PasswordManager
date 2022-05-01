<?php
/**
 * abCrypt utilizes openssl to encrypt and decrypt textstrings
 *
 * This project started as a way to encrypt user information which is stored in the database.
 *
 * @package asbraCMS
 * @subpackage abCrypt
 * @author Nimpen J. Nordström <j@asbra.nu>
 * @copyright 2018 ASBRA AB
 */

/**
 * abCrypt is a class for encrypting and decrypting textstrings using openssl
 *
 * @param string $encryption_key The encryption in HEX
 */
class abCrypt
{
  /** @var string $key Hex encoded binary key for encryption and decryption */
  public $key = '';

  /** @var string $encrypt_method Method to use for encryption */
  public  $encrypt_method = 'AES-256-CBC';

  /**
   * Construct our object and set encryption key, if exists.
   *
   * @param string $encryption_key Users binary encryption key in HEX encoding
   */
  function __construct ( $encryption_key = false )
  {
    if ( $key = hex2bin ( $encryption_key ) )
    {
      $this->key = $key;
    }
    else
    {
      echo "Key in construct does not appear to be HEX-encoded...";
    }
  }

  public function encrypt ( $string )
  {
    $new_iv = bin2hex ( random_bytes ( 8) );

    if ( $encrypted = base64_encode ( openssl_encrypt ( $string, $this->encrypt_method, $this->key, 0, $new_iv ) ) )
    {
      return $new_iv.':'.$encrypted;
    }
    else
    {
      return false;
    }
  }

  public function decrypt ( $string)
  {
    $parts     = explode(':', $string );
    $iv        = $parts[0];
    $encrypted = $parts[1];

    if ( $decrypted = openssl_decrypt ( base64_decode ( $encrypted ), $this->encrypt_method, $this->key, 0, $iv ) )
    {
      return $decrypted;
    }
    else
    {
      return false;
    }
  }
}