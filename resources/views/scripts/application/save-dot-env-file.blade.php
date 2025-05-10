@include('partials.scripts.shell-defaults')

cat > {!! $path !!} << 'EOF'
{!! trim($content) !!}
EOF
