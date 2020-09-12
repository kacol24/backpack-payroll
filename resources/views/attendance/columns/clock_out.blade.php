@php
    $column['text'] = empty($entry->end_at) ? '' :
            \Carbon\Carbon::parse($entry->end_at)
                ->locale(App::getLocale())
                ->format('d M Y, H:i:s');
@endphp
<span data-order="{{ $entry->end_at ?? '' }}">
	{{ $column['text'] }}
    @if(empty($entry->selfie_out))
    @else
        <img src="{{ asset('storage/' . $entry->selfie_out) }}" alt="selfie in" style="max-height: none;width: 100px;"
             class="d-block"/>
    @endif
</span>
