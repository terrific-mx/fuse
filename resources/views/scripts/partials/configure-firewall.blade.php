echo "Configure firewall with SSH port, HTTP and HTTPS"

ufw allow 22
ufw allow 80
ufw allow 443
yes | ufw enable
service ufw restart
