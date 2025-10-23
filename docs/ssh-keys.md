# SSH keys

Control server access for your organization

---

## List entries

See all public keys managed by your organization.

---

## Add entry

Enter a public key and name to create a new entry.

To display your public key, run:

```sh
cat ~/.ssh/id_rsa.pub
```

If you do not have a key, generate one with:

```sh
ssh-keygen -t rsa -b 4096
```

---

## Manage server access

Select servers to grant or revoke access for a key.  
Adding a server appends the key to `/home/fuse/.ssh/authorized_keys`.  
Removing a server deletes the key from that file.

---

## Remove entry

Delete a key and optionally remove it from all servers.  
You can also delete the key from the organization but keep it on selected servers.

---

## Set access on provisioning

Choose which keys are installed when creating new servers.
