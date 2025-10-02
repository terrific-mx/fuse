echo "Rename existing user 1000 if it exists, otherwise create a new user"

if getent passwd 1000 > /dev/null 2>&1; then
    echo "Renaming existing user 1000"
    OLD_USERNAME=$(getent passwd 1000 | cut -d: -f1)
    (pkill -9 -u $OLD_USERNAME || true)
    (pkill -KILL -u $OLD_USERNAME || true)
    usermod --login fuse --move-home --home /home/fuse $OLD_USERNAME
    groupmod --new-name fuse $OLD_USERNAME
else
    echo "Setup default user"
    useradd fuse
fi

echo "Create the user's home directory"

mkdir -p /home/fuse/.fuse
mkdir -p /home/fuse/.ssh

echo "Add user to groups"

adduser fuse sudo
id fuse
groups fuse

echo "Set shell"

chsh -s /bin/bash fuse

echo "Init default profile/bashrc"

cp /root/.bashrc /home/fuse/.bashrc
cp /root/.profile /home/fuse/.profile

echo "Copy SSH settings from root and create new key"

cp /root/.ssh/authorized_keys /home/fuse/.ssh/authorized_keys
cp /root/.ssh/known_hosts /home/fuse/.ssh/known_hosts
ssh-keygen -f /home/fuse/.ssh/id_rsa -t rsa -N ''

@if($sshKeys->isNotEmpty())
echo "Add SSH keys to authorized_keys"

@foreach($sshKeys as $sshKey)
cat <<EOF >> /home/fuse/.ssh/authorized_keys
{{ $sshKey->public_key }}
EOF

@endforeach
@endif

echo "Set password"

PASSWORD=$(mkpasswd -m sha-512 {{ $server->sudo_password }})
usermod --password $PASSWORD fuse

echo "Add default Caddy page"

mkdir -p /home/fuse/default
cat <<EOF >> /home/fuse/default/index.html
This server is managed by <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>.

EOF

echo "Fix user permissions"

chown -R fuse:fuse /home/fuse
chmod -R 755 /home/fuse
chmod 700 /home/fuse/.ssh
chmod 700 /home/fuse/.ssh/id_rsa
chmod 600 /home/fuse/.ssh/authorized_keys
