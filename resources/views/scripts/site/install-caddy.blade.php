@include('scripts.partials.shell-defaults')

# Create a temporary file with the new Caddyfile
cat > {!! $tempCaddyfilePath !!} << EOF
@if($site->hasWwwSubdomain())
    {!! substr($site->hostname, 4) !!}:{!! $site->port !!} {
        redir {scheme}://www.{host}{uri}
    }
@else
    www.{!! $site->hostname !!}:{!! $site->port !!} {
        redir {scheme}://{!! $site->hostname !!}{uri}
    }
@endif

# Do not remove this tls-* snippet
(tls-{!! $site->id !!}) {
    #
}

{!! $site->hostname !!}:{!! $site->port !!} {
    root * {!! $site->web_directory_path !!}
    encode zstd gzip

    import tls-{!! $site->id !!}

    header {
        -Server
        X-Content-Type-Options nosniff
        X-Frame-Options SAMEORIGIN
        X-Powered-By "{{ config('app.name') }}"
        X-XSS-Protection 1; mode=block
    }

    php_fastcgi unix/{!! $site->php_socket_path !!} {
        resolve_root_symlink
        try_files {path} {path}/index.html {path}/index.htm index.php
    }

	file_server

    log {
        output file {!! $site->path !!}/logs/caddy.log {
            roll_size 100mb
            roll_keep 30
            roll_keep_for 720h
        }
	}
}

EOF

# Validate the Caddyfile
set +e
caddy validate --config {!! $tempCaddyfilePath !!} --adapter caddyfile

# If the Caddyfile is invalid, remove the temporary file and exit
if [ $? -ne 0 ]; then
    rm {!! $tempCaddyfilePath !!}
    exit 1
fi

set -e

# Format the Caddyfile
caddy fmt {!! $tempCaddyfilePath !!} --overwrite

# Replace the old Caddyfile with the new one
mv {!! $tempCaddyfilePath !!} {!! $site->caddyfile_path !!}

# Reload Caddy
sudo /usr/sbin/service caddy reload
