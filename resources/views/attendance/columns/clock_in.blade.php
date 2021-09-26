@php
    $column['text'] = empty($entry->start_at) ? '' :
            \Carbon\Carbon::parse($entry->start_at)
                ->locale(App::getLocale())
                ->format('d M Y, H:i:s');
@endphp
<span data-order="{{ $entry->start_at ?? '' }}">
	{{ $column['text'] }}
    @if(empty($entry->selfie_in) || request('compact'))
    @else
        <img src="{{ asset('storage/' . $entry->selfie_in) }}" alt="selfie in" style="max-height: none;width: 100px;"
             class="d-block"/>
    @endif
</span>
