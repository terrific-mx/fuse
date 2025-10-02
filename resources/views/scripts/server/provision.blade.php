@include('scripts.partials.shell-defaults')

@include('scripts.partials.apt-functions')

{{-- Provision Steps --}}
@include('scripts.partials.configure-swap')
@include('scripts.partials.configure-firewall')
@include('scripts.partials.apt-update-upgrade')
@include('scripts.partials.install-essential-packages')
@include('scripts.partials.setup-unattended-upgrades')
@include('scripts.partials.setup-root')
@include('scripts.partials.ssh-security')
@include('scripts.partials.setup-default-user')

{{-- Software Stack --}}
@include('scripts.partials.caddy-2')
@include('scripts.partials.mysql-80')
{{-- Redis should be installed before PHP --}}
@include('scripts.partials.redis-6')
@include('scripts.partials.php-81')
@include('scripts.partials.php-82')
@include('scripts.partials.php-83')
@include('scripts.partials.composer-2')
@include('scripts.partials.node-22')

# See 'apt-update-upgrade'
waitForAptUnlock
apt-mark unhold cloud-init
