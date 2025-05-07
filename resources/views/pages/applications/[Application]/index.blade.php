<?php

use App\Models\Application;
use Livewire\Volt\Component;

new class extends Component {
    public Application $application;
}; ?>

<x-layouts.app>
    @volt('pages.applications.index')
        <x-applications.layout :application="$application">
            <!-- // -->
        </x-applications.layout>
    @endvolt
</x-layouts.app>
