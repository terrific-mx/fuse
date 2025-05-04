@include('partials.scripts.shell-defaults')

@include('partials.scripts.apt-functions')

{{-- Provision Steps --}}
@include('scripts.server._configure-swap')
@include('scripts.server._configure-firewall')
@include('scripts.server._apt-update-upgrade')
@include('scripts.server._install-essential-packages')
@include('scripts.server._setup-unattended-upgrades')
@include('scripts.server._setup-root')
@include('scripts.server._ssh-security')
@include('scripts.server._setup-default-user')

{{-- Software Stack --}}
@include('scripts.server._caddy-2')
@include('scripts.server._mysql-80')
{{-- Redis should be installed before PHP --}}
@include('scripts.server._redis-6')
@include('scripts.server._php-81')
@include('scripts.server._php-82')
@include('scripts.server._php-83')
@include('scripts.server._composer-2')
@include('scripts.server._node-22')

# See 'apt-update-upgrade'
waitForAptUnlock
apt-mark unhold cloud-init
