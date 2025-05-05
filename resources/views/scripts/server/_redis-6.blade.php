@include('partials.scripts.apt-functions')

echo "Install Redis"

waitForAptUnlock
apt-get install -y valkey-server
sed -i 's/bind 127.0.0.1/bind 0.0.0.0/' /etc/valkey/valkey.conf
service valkey-server restart
systemctl enable valkey-server
