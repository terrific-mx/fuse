@include('partials.scripts.shell-defaults')

cat <<EOF >>/home/{{ $username }}/.ssh/authorized_keys

{{ $publicKey }}
EOF
