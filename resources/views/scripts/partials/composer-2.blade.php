echo "Download and install Composer dependency manager"

curl -sS https://getcomposer.org/installer | php -- --2
mv composer.phar /usr/local/bin/composer

echo "fuse ALL=(root) NOPASSWD: /usr/local/bin/composer self-update*" > /etc/sudoers.d/composer

# Create default auth.json

mkdir -p /home/fuse/.config/composer
touch /home/fuse/.config/composer/auth.json

cat > /home/fuse/.config/composer/auth.json << 'EOF'
{
  "bearer": {},
  "bitbucket-oauth": {},
  "github-oauth": {},
  "gitlab-oauth": {},
  "gitlab-token": {},
  "http-basic": {}
}
EOF

chown -R fuse:fuse /home/fuse/.config/composer
chmod 600 /home/fuse/.config/composer/auth.json
