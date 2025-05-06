cd {!! $repositoryDirectory !!}

GIT_HASH=$(git rev-list {!! $application->branch !!} -1);
