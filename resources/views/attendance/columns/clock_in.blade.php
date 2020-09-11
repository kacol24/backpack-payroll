<span data-order="{{ $entry->start_at }}">
	{{ \Carbon\Carbon::parse($entry->start_at)
            ->locale(app()->getLocale())
            ->isoFormat(config('backpack.base.default_datetime_format')) }}
    @if(empty($entry->selfie_in))
        [no selfie]
    @else
        <img src="{{ asset('storage/' . $entry->selfie_in) }}" alt="selfie in" style="max-height: none;width: 100px;"
             class="ml-3"/>
    @endif
</span>
