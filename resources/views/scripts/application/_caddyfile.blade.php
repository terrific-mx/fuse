@if($domainStartsWithWww)
    {!! substr($address, 4) !!}:{!! $port !!} {
        redir {scheme}://www.{host}{uri}
    }
@else
    www.{!! $address !!}:{!! $port !!} {
    redir {scheme}://{!! $address !!}{uri}
    }
@endif

# Do not remove this tls-* snippet
@include('scripts.application._tls-snippet', ['tlsSetting' => $tlsSetting])

{!! $address !!}:{!! $port !!} {
    root * {!! $application->web_directory !!}
    encode zstd gzip

    import tls-{!! $application->id !!}

    header {
        -Server
        X-Content-Type-Options nosniff
        X-Frame-Options SAMEORIGIN
        X-Powered-By "{{ config('app.name') }}"
        X-XSS-Protection 1; mode=block
    }

    @if($application->type !== 'static')
        php_fastcgi unix/{!! $phpSocket !!} {
            resolve_root_symlink
            try_files {path} {path}/index.html {path}/index.htm index.php
        }
    @endif

    @if($application->type === 'wordpress')
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
