(tls-{!! $application->id !!}) {
    @if($tlsSetting === 'custom' && $certificate)
        tls {!! $certificate->certificatePath() !!} {!! $certificate->privateKeyPath() !!}
    @elseif($tlsSetting === 'internal')
        tls internal
    @else
        #
    @endif
}
