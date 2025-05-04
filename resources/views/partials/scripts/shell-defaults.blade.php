set -{{ implode(array_filter([
    ($exitImmediately ?? true) ? 'e' : null,
    'u',
])) }}
export DEBIAN_FRONTEND=noninteractive
