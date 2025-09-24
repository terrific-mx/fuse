# Hetzner Server Creation: Required Fields & Steps

## Required Fields for Hetzner Cloud API

When creating a server via Hetzner Cloud API, you must provide:

- **name**: The name for your server (string)
- **server_type**: The type/plan (e.g. cx11, cx22, cpx21, etc.)
- **location**: The datacenter location (e.g. fsn1, nbg1, hel1, ash, hil, sin)
- **image**: The OS image (e.g. ubuntu-24.04, debian-12, etc.)

**Optional/Advanced fields:**
- **ssh_keys**: Array of SSH key IDs or names to inject for root access
- **user_data**: Cloud-init script for custom provisioning
- **labels**: Key-value pairs for tagging
- **firewalls**: Firewall IDs to attach
- **volumes**: Volume IDs to attach
- **network**: Network IDs for private networking

---

## Typical UI Steps for Creating a Hetzner Server

1. **Name**: Enter a name for the server
2. **Server Type**: Select from available types (fetch from Hetzner API)
3. **Location**: Select datacenter (fetch from Hetzner API)
4. **Image/OS**: Select operating system (fetch from Hetzner API)
5. **SSH Key**: Select or add SSH key (fetch from Hetzner API)
6. *(Optional)*: Advanced options (firewall, volumes, network, user data)
7. **Create**: Submit to provision the server

---

## Backend Steps

- Validate all required fields
- Use Hetzner API (POST to `https://api.hetzner.cloud/v1/servers`) with the above fields
- Store the returned server ID, IP address, and status in your database
- Show feedback and status to the user

---

## Example API Call

```bash
curl -H 'Authorization: Bearer YOUR-API-TOKEN' \
     -H 'Content-Type: application/json' \
     -d '{ "name": "server01", "server_type": "cx22", "location": "nbg1", "image": "ubuntu-24.04"}' \
     -X POST 'https://api.hetzner.cloud/v1/servers'
```

---

## Summary Table: Hetzner Server Creation Fields

| Field         | Type    | Required | Example Value         | Description                       |
|---------------|---------|----------|-----------------------|-----------------------------------|
| name          | string  | Yes      | "server01"            | Server name                       |
| server_type   | string  | Yes      | "cx22"                | Server type/plan                  |
| location      | string  | Yes      | "nbg1"                | Datacenter location               |
| image         | string  | Yes      | "ubuntu-24.04"        | OS image                          |
| ssh_keys      | array   | No       | ["my-key"]            | SSH key(s) for root access        |
| user_data     | string  | No       | "#cloud-config..."    | Cloud-init script                 |
| labels        | object  | No       | {"env":"prod"}        | Key-value tags                    |
| firewalls     | array   | No       | [12345]               | Firewall IDs                      |
| volumes       | array   | No       | [67890]               | Volume IDs                        |
| network       | array   | No       | [11111]               | Network IDs                       |

---

## Next Steps

- Update your UI to collect these fields (name, server_type, location, image, ssh_keys).
- Update your backend to call Hetzner API with these fields and handle the response.
- Store Hetzner server details (ID, IP, status) in your database.

**If you want to see the full list of available server types, locations, images, or SSH keys, you need to fetch them from Hetzner API endpoints before showing the form.**

---

**If you need help with the actual implementation (UI, backend, API integration), let me know! I can guide you step-by-step.**
