<x-mail::message>
{{-- Greeting --}}
@if (!empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('mail.greeting')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}
@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
$color = match ($level) {
'success', 'error' => $level,
default => 'primary',
};
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}
@endforeach

{{-- Salutation --}}
@if (!empty($salutation))
{{ $salutation }}
@else
@lang('mail.salutation'),<br>
@lang('mail.signature')
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang('mail.action_backup', ['actionText' => $actionText])
<span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>
