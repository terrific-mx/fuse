@include('partials.scripts.apt-functions')

echo "Install Node 22"

waitForAptUnlock
curl --silent --location https://deb.nodesource.com/setup_22.x | bash -
apt-get update
waitForAptUnlock

apt-get install -y --force-yes nodejs

echo "Install Node Packages"

npm install -g fx gulp n pm2 svgo yarn zx
