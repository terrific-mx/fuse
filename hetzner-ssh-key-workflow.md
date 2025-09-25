# Hetzner SSH Key Workflow for Server Creation

## Overview

When creating a server in Hetzner Cloud using an organization's SSH public key, you must first register the key with Hetzner and use the returned key ID during server creation. This document outlines the required steps and design considerations for integrating this workflow into the application.

---

## 1. Hetzner SSH Key Workflow

- Hetzner Cloud requires SSH keys to be registered via their API before they can be injected into new servers.
- When creating a server, reference the SSH key by its Hetzner-assigned `id` (not the raw public key string).

---

## 2. Current Data Model Context

- **Organization model**: Has an `ssh_public_key` attribute. The organization's SSH key never changes.
- **ServerProvider model**: Stores Hetzner API credentials and is linked to Organization. The Hetzner SSH key ID will be stored in this model.
- **HetznerService**: Handles API calls to Hetzner, but currently does not manage SSH keys.

---

## 3. Required Steps for Server Creation with SSH Key

### Step 1: Add SSH Key to Hetzner
- Use the Hetzner API endpoint `POST /ssh_keys` to upload the organization's `ssh_public_key`.
- The response will include a unique `id` for the SSH key.

### Step 2: Store Hetzner SSH Key ID
- Save the returned `id` in your database, specifically in the ServerProvider model.
- This avoids uploading the same key multiple times and allows you to reference it when creating servers.

### Step 3: Create Server with SSH Key
- When calling `POST /servers` to create a server, include the SSH key's Hetzner `id` in the `ssh_keys` array parameter.
- This ensures the server is provisioned with the correct public key for access.

---

## 4. Design Considerations

- **Where to store the Hetzner SSH key ID?**
  - Store the Hetzner SSH key ID in the ServerProvider model (e.g., `hetzner_ssh_key_id`).

- **Avoid duplicate uploads:**
  - Before uploading, check if the key already exists in Hetzner (`GET /ssh_keys` and compare fingerprints).
  - If it exists, use the existing `id`; otherwise, upload and store the new `id`.

- **Key lifecycle:**
  - The organization's SSH key never changes, so no lifecycle management is needed.

---

## 5. API Endpoints Involved (Hetzner Cloud)
- `POST /ssh_keys` — Add a new SSH key.
- `GET /ssh_keys` — List existing SSH keys.
- `POST /servers` — Create a server, referencing SSH key IDs.

---

## 6. Summary of Required Changes
- Add logic to HetznerService to manage SSH keys (upload, check existence, store ID).
- Update data model to store Hetzner SSH key ID.
- Update server creation logic to include SSH key ID.

---

*No implementation code is included in this document.*
