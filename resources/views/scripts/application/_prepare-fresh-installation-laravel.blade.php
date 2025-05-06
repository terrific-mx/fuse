cd {!! $application->path() !!}

ENV_PATH="{!! $zeroDowntimeDeployment ? $sharedDirectory : $repositoryDirectory !!}/.env"
EXAMPLE_ENV_PATH="{!! $zeroDowntimeDeployment ? $releaseDirectory : $repositoryDirectory !!}/.env.example"

if [ ! -f $ENV_PATH ] && [ -f $EXAMPLE_ENV_PATH ]; then
    cp $EXAMPLE_ENV_PATH $ENV_PATH
    cd {!! $zeroDowntimeDeployment ? $sharedDirectory : $repositoryDirectory !!}

    @foreach($environmentVariables as $search => $replace)
        sed -i --follow-symlinks "s|^{{ $search }}=.*|{{ $search }}={{ $replace }}|g" .env
    @endforeach

fi
