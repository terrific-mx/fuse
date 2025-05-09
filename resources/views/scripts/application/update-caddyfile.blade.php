@include('partials.scripts.shell-defaults')

# Create a temporary file with the new Caddyfile
cat > {!! $tmpCaddyFilePath !!} << EOF
{!! $caddyfile !!}

EOF

# Validate the Caddyfile
set +e
caddy validate --config {!! $tmpCaddyFilePath !!} --adapter caddyfile

# If the Caddyfile is invalid, remove the temporary file and exit
if [ $? -ne 0 ]; then
    rm {!! $tmpCaddyFilePath !!}
    exit 1
fi

set -e

# Format the Caddyfile
caddy fmt {!! $tmpCaddyFilePath !!} --overwrite

# Replace the old Caddyfile with the new one
mv {!! $tmpCaddyFilePath !!} {!! $caddyfilePath !!}

# Reload Caddy
sudo /usr/sbin/service caddy reload
