<!-- number input -->
@include('crud::fields.inc.wrapper_start')
<label for="{{ $field['name'] }}">{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')

@if(isset($field['prefix']) || isset($field['suffix']))
    <div class="input-group"> @endif
        @if(isset($field['prefix']))
            <div class="input-group-prepend"><span class="input-group-text">{!! $field['prefix'] !!}</span></div> @endif
        <input
            type="tel"
            data-money
            data-init-function="initMaskMoneyElement"
            name="{{ $field['name'] }}"
            value="{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '' }}"
            @include('crud::fields.inc.attributes')
        >
        @if(isset($field['suffix']))
            <div class="input-group-append">{!! $field['suffix'] !!}</div> @endif

        @if(isset($field['prefix']) || isset($field['suffix'])) </div> @endif

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')


@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')

    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <script>
            function initMaskMoneyElement(element) {
                new Cleave(element, {
                    numeral: true,
                    numeralDecimalMark: ',',
                    delimiter: '.',
                    numeralDecimalScale: 0
                });
            }
        </script>
    @endpush
@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
