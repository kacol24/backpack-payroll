<span data-order="{{ $entry->start_at }}">
	{{ \Carbon\Carbon::parse($entry->start_at)
            ->locale(app()->getLocale())
            ->format('d M Y, H:i:s') }}
    @if(empty($entry->selfie_in))
    @else
        <img src="{{ asset('storage/' . $entry->selfie_in) }}" alt="selfie in" style="max-height: none;width: 100px;"
             class="d-block"/>
    @endif
</span>
