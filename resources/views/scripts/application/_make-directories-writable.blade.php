@foreach($application->writable_directories as $directory)
    DIRECTORY_IS_WRITEABLE=$(getfacl -p {!! $releaseDirectory !!}/{!! $directory !!} | grep "^user:{!! $application->server->username !!}:.*w" | wc -l)

    if [ $DIRECTORY_IS_WRITEABLE -eq 0 ]; then
        # Make the directory writable (without sudo)
        setfacl -L -m u:{!! $application->server->username !!}:rwX {!! $releaseDirectory !!}/{!! $directory !!}
        setfacl -dL -m u:{!! $application->server->username !!}:rwX {!! $releaseDirectory !!}/{!! $directory !!}
    fi

@endforeach
