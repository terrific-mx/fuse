mkdir -p {!! $directory !!}

cat > {!! $path !!} << 'EOF'
{!! trim($contents) !!}
EOF
