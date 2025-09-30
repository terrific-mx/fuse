# Organization SSH Key Management Plan

## Overview
This plan describes how to securely generate, store, and use SSH key pairs for each organization in the application. SSH key pairs will be generated using the `ssh-keygen` command (invoked via Laravel’s `Process` facade), and the key values (not file paths) will be stored directly in the `organizations` table. When needed for SSH operations, the private key will be written to Laravel application storage in a minimal folder structure.

---

## 1. Update Migration
- Add `ssh_private_key` (text, encrypted if possible) and `ssh_public_key` (text) fields to the existing `create_organizations_table` migration.
- These fields will store the actual SSH key values, not file paths.

## 2. Key Generation & Storage
- On organization creation (or first use), generate an SSH key pair using the `ssh-keygen` command.
- Use Laravel’s `Process` facade to run the `ssh-keygen` command, outputting the keys to a temporary location.
- Read the contents of the generated private and public key files.
- Store the private and public key values directly in the new fields in the database.
- Delete the temporary key files after reading their contents.

## 3. Key Retrieval for SSH
- When a task needs to SSH, retrieve the key values from the organization record.
- Write the private key to a file in Laravel application storage, e.g., `storage/app/ssh-keys/org_{org_id}_id_rsa`, with secure permissions (`0600`).
- Use this file in the SSH command.
- Delete the temporary file after use if possible.

## 4. Security
- Store the private key encrypted in the database if possible (using Laravel’s built-in encryption).
- Ensure the temporary file is only readable by the application.
- Never expose the private key in logs or responses.
- Clean up temporary key files after use.

## 5. Public Key Usage
- The public key can be used for provisioning and can be safely displayed or distributed as needed.

---

**Summary:**
- The `organizations` table will have `ssh_private_key` and `ssh_public_key` fields containing the actual key values.
- SSH keys are generated using `ssh-keygen` via Laravel’s `Process` facade, read into memory, and stored in the database.
- For SSH operations, the private key is written to a file in `storage/app/org-ssh-tmp/` with a flat, minimal folder structure, and deleted after use.
- Security is maintained by encrypting the private key in the database and using secure file permissions.
