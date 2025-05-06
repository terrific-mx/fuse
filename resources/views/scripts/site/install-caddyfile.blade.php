@include('partials.scripts.shell-defaults')

# Create the necessary directories
mkdir -p {!! $site->path() !!}

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

# Add default Caddy page
mkdir -p {{ $webDirectory }}
cat <<EOF >> {{ $webDirectory }}/index.html
This server is managed by <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>.

EOF
