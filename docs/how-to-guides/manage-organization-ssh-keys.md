# How to Manage Organization SSH Keys

This guide explains how to add, authorize, and remove SSH keys for your organization’s servers.

## Overview

Organization SSH keys allow you to control which users and servers have secure access. You can add new keys, authorize them for specific servers, and remove them as needed.

## Adding an SSH Key

1. **Go to the Organization SSH Keys page.**
2. **Click “Add Key.”**
3. **Enter a name** for the key (for identification).
4. **Paste the public SSH key** (the contents of your `.pub` file).
5. **Save** the key.

> When you add a key, it is appended to the `authorized_keys` file for the `fuse` user on selected servers.

## Authorizing a Key for Servers

After adding a key, you can control which servers it can access:

1. **Select the key** you want to manage.
2. **Choose the servers** where the key should be authorized.
    - To **authorize** a key, select new servers and save. The key will be installed on those servers.
    - To **remove authorization**, deselect servers and save. The key will be uninstalled from those servers.

## Deleting an SSH Key

You have two options when deleting a key:

- **Delete the key and remove it from all servers:**  
  The key will be deleted from your organization and uninstalled from all servers.
- **Delete the key only (keep on servers):**  
  The key will be removed from your organization, but will remain authorized on any servers where it was previously installed.

## Provisioning New Servers

When provisioning a new server, you can select which organization SSH keys should be authorized. The selected keys will be installed automatically.

## How Key Installation and Removal Works

- **Adding a key:**  
  The public key is appended to the `authorized_keys` file for the `fuse` user on the selected servers.
- **Removing a key:**  
  The public key is removed from the `authorized_keys` file for the `fuse` user on the deselected servers.

---

**Category:** How-to Guide
