# User SSH Key Management

This document describes how to generate a unique SSH keypair for each registered user, store the public key, and securely encrypt the private key in the database using Laravel and the `ssh-keygen` command.

---

## Overview

- **Keypair Generation:** Use the `ssh-keygen` command to generate a standard SSH keypair for each user upon registration.
- **Encryption:** Use Laravel's encrypted attribute casting to automatically encrypt the private key before storing it in the database.
- **Storage:** Store the public key in plaintext and the encrypted private key in the database.
- **Security:** Never expose the private key in the UI or logs. Only decrypt when needed for internal operations.

---

## Migration

Add the following columns to the `users` table:

- `ssh_public_key` (text, nullable)
- `ssh_private_key` (text, nullable)

> **Best Practice:** Use Laravel's encrypted attribute casting for `ssh_private_key`. This automatically encrypts the value when saving and decrypts when retrieving, with no manual encryption/decryption required.

---

## Key Generation Process

1. **Run `ssh-keygen` as a process:**
   - Example command:
     ```bash
     ssh-keygen -t rsa -b 4096 -f /tmp/laravel_userkey_{user_id} -N ""
     ```
     - `-t rsa`: RSA key type
     - `-b 4096`: 4096 bits
     - `-f /tmp/laravel_userkey_{user_id}`: Output file prefix
     - `-N ""`: No passphrase

2. **Read the generated key files:**
   - Private key: `/tmp/laravel_userkey_{user_id}`
   - Public key: `/tmp/laravel_userkey_{user_id}.pub`

3. **Store keys in the database:**
   - Save the public key as plaintext
   - Save the private key to the model attribute with encrypted cast (Laravel will encrypt automatically)

5. **Clean up:**
   - Delete the temporary key files after storing

---

## Example Laravel Implementation

### Model Setup (Encrypted Cast)

```php
class User extends Model
{
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ssh_private_key' => 'encrypted',
        ];
    }
}
```

### Key Generation and Storage

```php
use Illuminate\Support\Facades\Process;

$userId = $user->id;
$keyPath = "/tmp/laravel_userkey_{$userId}";

// Generate SSH keypair
Process::run("ssh-keygen -t rsa -b 4096 -f {$keyPath} -N ''");

// Read keys
$privateKey = file_get_contents($keyPath);
$publicKey = file_get_contents("{$keyPath}.pub");

// Store in database (private key is automatically encrypted via cast)
$user->ssh_public_key = $publicKey;
$user->ssh_private_key = $privateKey;
$user->save();

// Delete temp files
unlink($keyPath);
unlink("{$keyPath}.pub");
```

### Usage

```php
// Retrieve and use the decrypted private key
$privateKey = $user->ssh_private_key;
```

> **Note:** No need to use the Crypt facade. Encryption and decryption are handled automatically by Laravel's encrypted cast.

---

## Security Considerations

- **Never expose the private key in the UI or logs.**
- **Decrypt the private key only when needed for internal operations.**
- **Use encrypted attribute casting for automatic encryption/decryption.**
- **No need to use the Crypt facade for this attribute.**
- **Rotate encryption keys using Laravel's key rotation features if required.**
- **Handle decryption exceptions gracefully if accessing the attribute directly.**

---

## References
- [Laravel Encryption Documentation](https://laravel.com/docs/11.x/encryption)
- [ssh-keygen Manual](https://man.openbsd.org/ssh-keygen)

---

This approach ensures each user has a unique, secure SSH keypair, with the private key protected using strong encryption and best practices.