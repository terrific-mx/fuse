# Check if the repository exists and if the remote URL is correct, if not, delete it
if [ -f "{!! $repositoryDirectory !!}/HEAD" ]; then
    cd {!! $repositoryDirectory !!}
    CURRENT_REMOTE_URL=$(git config --get remote.origin.url || echo '');

    if [ "$CURRENT_REMOTE_URL" != '{!! $repositoryUrl !!}' ]; then
        @if($zeroDowntimeDeployment)
            rm -rf {!! $repositoryDirectory !!}
            cd {!! $application->path() !!}
            mkdir -p {!! $repositoryDirectory !!}
        @else
            git remote set-url origin {!! $repositoryUrl !!}
        @endif

    fi
fi

@if($deployKeyPrivate)
    # Store the deploy key and set the GIT SSH command
cat <<EOF >> {!! $application->path() !!}/deploy_key
{{ $deployKeyPrivate }}
EOF

    chmod 600 {!! $application->path() !!}/deploy_key
    export GIT_SSH_COMMAND="ssh -i {!! $application->path() !!}/deploy_key"
@endif

cd {!! $application->path() !!}

# Clone the repository if it doesn't exist
@if($zeroDowntimeDeployment)
    if [ ! -f "{!! $repositoryDirectory !!}/HEAD" ]; then
        git clone --mirror {!! $repositoryUrl !!} {!! $repositoryDirectory !!}
    fi
@else
    if [ ! -f "{!! $repositoryDirectory !!}/.git/HEAD" ]; then
        git clone {!! $repositoryUrl !!} {!! $repositoryDirectory !!}
    fi
@endif

# Fetch the latest changes from the repository
cd {!! $repositoryDirectory !!}

@if($zeroDowntimeDeployment)
    git remote update
@else
    git pull origin {!! $application->branch !!}
@endif

@if($zeroDowntimeDeployment)
    # Clone the repository into the release directory
    cd {!! $releaseDirectory !!}
    git clone -l {!! $repositoryDirectory !!} .
    git checkout --force {!! $application->branch !!}
@endif

cd {!! $application->path() !!}
