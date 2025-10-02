@include('scripts.partials.shell-defaults')

export PHP_BINARY={!!  $site->php_binary !!}

# Create the necessary directories
mkdir -p {!! $site->repository_directory !!}
mkdir -p {!! $site->shared_directory !!}
mkdir -p {!! $deployment->release_directory !!}
mkdir -p {!! $site->logs_directory !!}

# Cleanup old releases
DEPLOYMENT_KEEP="{{ $site->latestFinishedDeployment ? $site->latestFinishedDeployment->created_at->timestamp : 'none' }}"

# Get a list of all deployments, sorted by timestamp in ascending order
DEPLOYMENT_LIST=($(ls -1 {!! $site->releases_directory !!} | sort -n))

# Determine how many deployments to delete
NUM_TO_DELETE=$((${#DEPLOYMENT_LIST[@]} - {{ $site->releases_retention }}))

# Loop through the deployments to delete
for ((i=0; i<$NUM_TO_DELETE; i++)); do
    DEPLOY=${DEPLOYMENT_LIST[$i]}
    # Skip the deployment to keep
    if [[ $DEPLOY == $DEPLOYMENT_KEEP ]]; then
        continue
    fi

    # Delete the deployment
    rm -rf {!! $site->releases_directory !!}/$DEPLOY
done

@if($site->script_before_deploy)
    echo "Running script before deploying"
    cd {!! $site->releases_directory !!}
    {!! $site->script_before_deploy !!}
@endif

@if($site->repository_url)
    # Check if the repository exists and if the remote URL is correct, if not, delete it
    if [ -f "{!! $site->repository_directory !!}/HEAD" ]; then
        cd {!! $site->repository_directory !!}
        CURRENT_REMOTE_URL=$(git config --get remote.origin.url || echo '');

        if [ "$CURRENT_REMOTE_URL" != '{!! $site->repository_url !!}' ]; then
            rm -rf {!! $site->repository_directory !!}
            cd {!! $site->path !!}
            mkdir -p {!! $site->repository_directory !!}
        fi
    fi

    cd {!! $site->path !!}

    # Clone the repository if it doesn't exist
    if [ ! -f "{!! $site->repository_directory !!}/HEAD" ]; then
        git clone --mirror {!! $site->repository_url !!} {!! $site->repository_directory !!}
    fi

    # Fetch the latest changes from the repository
    cd {!! $site->repository_directory !!}

    git remote update

    # Clone the repository into the release directory
    cd {!! $deployment->release_directory !!}
    git clone -l {!! $site->repository_directory !!} .
    git checkout --force {!! $site->repository_branch !!}

    cd {!! $site->path !!}

    @if($site->script_after_deploy)
        echo "Running script after updating repository"
        cd {!! $deployment->release_directory !!}
        {!! $site->script_after_deploy !!}
    @endif

@endif

@unless($site->installed_at)
    cd {!! $site->path !!}

    cd {!! $site->path !!}

    ENV_PATH="{!! $site->shared_directory !!}/.env"
    EXAMPLE_ENV_PATH="{!! $deployment->release_directory !!}/.env.example"

    if [ ! -f $ENV_PATH ] && [ -f $EXAMPLE_ENV_PATH ]; then
        cp $EXAMPLE_ENV_PATH $ENV_PATH
        cd {!! $site->shared_directory !!}

        @foreach($site->environment_variables as $search => $replace)
            sed -i --follow-symlinks "s|^{{ $search }}=.*|{{ $search }}={{ $replace }}|g" .env
        @endforeach

    fi

    cd {!! $site->path !!}
@endunless

@foreach($site->shared_directories as $directory)
    if [ ! -d "{!! $site->shared_directory !!}/{!! $directory !!}" ]; then
        # Create shared directory if it does not exist.
        mkdir -p {!! $site->shared_directory !!}/{!! $directory !!}

        if [ -d "{!! $deployment->release_directory !!}/{!! $directory !!}" ]; then
            # Copy contents of release directory to shared directory if it exists.
            cp -r {!! $deployment->release_directory !!}/{!! $directory !!} {!! $site->shared_directory !!}/{!! dirname($directory) !!}
        fi
    fi

    #  Remove shared directory from release directory if it exists.
    rm -rf {!! $deployment->release_directory !!}/{!! $directory !!}

    # Create parent directory of shared directory in release directory if it does not exist,
    # otherwise symlink will fail.
    mkdir -p `dirname {!! $deployment->release_directory !!}/{!! $directory !!}`

    # Symlink shared directory to release directory.
    ln -nfs --relative {!! $site->shared_directory !!}/{!! $directory !!} {!! $deployment->release_directory !!}/{!! $directory !!}

@endforeach

@foreach($site->shared_files as $file)
    # Create directories in shared and release directories if they don't exist
    mkdir -p {!! $deployment->release_directory !!}/{!! dirname($file) !!}
    mkdir -p {!! $site->shared_directory !!}/{!! dirname($file) !!}

    # If the shared file does not exist, but the release file does, copy the release file to shared
    if [ ! -f "{!! $site->shared_directory !!}/{!! $file !!}" ] && [ -f "{!! $deployment->release_directory !!}/{!! $file !!}" ]; then
        cp {!! $deployment->release_directory !!}/{!! $file !!} {!! $site->shared_directory !!}/{!! $file !!}
    fi

    # If the shared file still does not exist, create it
    if [ ! -f "{!! $site->shared_directory !!}/{!! $file !!}" ]; then
        touch {!! $site->shared_directory !!}/{!! $file !!}
    fi

    # If the release file exists, remove it
    if [ -f "{!! $deployment->release_directory !!}/{!! $file !!}" ]; then
        rm -rf {!! $deployment->release_directory !!}/{!! $file !!}
    fi

    # Create symlink
    ln -nfs --relative {!! $site->shared_directory !!}/{!! $file !!} {!! $deployment->release_directory !!}/{!! $file !!}

@endforeach

@foreach($site->writable_directories as $directory)
    DIRECTORY_IS_WRITEABLE=$(getfacl -p {!! $deployment->release_directory !!}/{!! $directory !!} | grep "^user:fuse:.*w" | wc -l)

    if [ $DIRECTORY_IS_WRITEABLE -eq 0 ]; then
        # Make the directory writable (without sudo)
        setfacl -L -m u:fuse:rwX {!! $deployment->release_directory !!}/{!! $directory !!}
        setfacl -dL -m u:fuse:rwX {!! $deployment->release_directory !!}/{!! $directory !!}
    fi

@endforeach

@if($site->script_before_activate)
    echo "Running script before putting the site live"
    cd {!! $deployment->release_directory !!}
    {!! $site->script_before_activate !!}
@endif

cd {!! $site->path !!}
ln -nfs --relative {!! $deployment->release_directory !!} {!! $site->current_directory !!}

@if($site->script_after_activate)
    echo "Running script after putting the site live"
    cd {!! $deployment->release_directory !!}
    {!! $site->script_after_activate !!}
@endif

echo "Done!"
