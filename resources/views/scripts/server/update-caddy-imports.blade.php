# Update Caddy site imports

cat > /etc/caddy/Sites.caddy << EOF
# import /home/eddy/example.com/Caddyfile
@foreach($applications as $application)
import {!! $application->path() !!}/Caddyfile

@endforeach

EOF

# Reload Caddy
/usr/sbin/service caddy reload
