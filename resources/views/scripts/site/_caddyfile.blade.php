@if($domainStartsWithWww)
    {!! substr($site->address, 4) !!}:{!! $site->port !!} {
        redir {scheme}://www.{host}{uri}
    }
@else
    www.{!! $site->address !!}:{!! $site->port !!} {
        redir {scheme}://{!! $site->address !!}{uri}
    }
@endif

# Do not remove this tls-* snippet
@include('scripts.site._tls-snippet', ['tlsSetting' => $tlsSetting])

{!! $address !!}:{!! $port !!} {
    root * {!! $webDirectory !!}
    encode zstd gzip

    import tls-{!! $site->id !!}

    header {
        -Server
        X-Content-Type-Options nosniff
        X-Frame-Options SAMEORIGIN
        X-Powered-By "{{ config('app.name') }}"
        X-XSS-Protection 1; mode=block
    }

    @if($siteType !== 'static')
        php_fastcgi unix/{!! $phpSocket !!} {
            resolve_root_symlink
            try_files {path} {path}/index.html {path}/index.htm index.php
        }
    @endif

    @if($site->type === 'wordpress')
        @disallowed {
            path /xmlrpc.php
            path *.sql
            path /wp-content/uploads/*.php
        }

        rewrite @disallowed '/index.php'
    @endif

	file_server

    log {
        output file {!! $path !!}/logs/caddy.log {
            roll_size 100mb
            roll_keep 30
            roll_keep_for 720h
        }
	}
}
