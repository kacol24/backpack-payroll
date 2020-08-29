@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
      $crud->entity_name_plural => url($crud->route),
      trans('backpack::crud.preview') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="container-fluid d-print-none">
        <a href="javascript: window.print();" class="btn float-right"><i class="la la-print"></i></a>
        <a href="{{ route('payslip.edit', $entry) }}" class="btn float-right">
            <i class="la la-edit"></i> Edit
        </a>
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
            <small>{!! $crud->getSubheading() ?? mb_ucfirst(trans('backpack::crud.preview')).' '.$crud->entity_name !!}
                .</small>
            @if ($crud->hasAccess('list'))
                <small class=""><a href="{{ url($crud->route) }}" class="font-sm"><i
                            class="la la-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }}
                        <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0">
                <div class="card-header">
                    <h3 class="card-title d-flex align-items-end justify-content-between m-0">
                        Slip Gaji
                        <small class="float-right">
                            Periode: {{ $entry->period->format('F Y') }}
                        </small>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-3">
                            <img src="{{ asset('images/logo-square.png') }}" alt="company logo" class="img-fluid w-100">
                        </div>
                        <div class="col-6">
                            <address>
                                <strong>
                                    Kamsia Boba Loop
                                </strong><br>
                                Loop Plaza Graha Famili<br>
                                Jl. Mayjen Yono Suwoyo No. 28<br>
                                Dukuhpakis, Surabaya, Jawa Timur 60226<br>
                                +62851 5504 3789
                            </address>
                        </div>
                    </div>
                    <div class="row my-5 justify-content-between">
                        <div class="col-6">
                            <table class="w-100">
                                <tbody>
                                <tr>
                                    <td>
                                        <strong class="d-block">
                                            Nama
                                        </strong>
                                        {{ $entry->employee->name }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="w-100">
                                <tbody>
                                <tr>
                                    <th>
                                        Nomor Slip
                                    </th>
                                    <td>
                                        : {{ $entry->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Tanggal Pembayaran:
                                    </th>
                                    <td>
                                        : {{ $entry->paid_at->format('d F Y') }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <table class="table table-responsive-sm table-sm border">
                        <thead>
                        <tr class="text-center">
                            <th class="w-50 border-right">
                                DESKRIPSI
                            </th>
                            <th class="w-25 border-right">
                                PENDAPATAN
                            </th>
                            <th class="w-25">
                                POTONGAN
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="border-right p-0">&nbsp;</td>
                            <td class="border-right p-0">&nbsp;</td>
                            <td class="p-0">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="border-right border-top-0">
                                Gaji pokok
                            </td>
                            <td class="text-right border-right border-top-0">
                                {{ $entry->formatted_gross_pay }}
                            </td>
                            <td class="border-top-0"></td>
                        </tr>
                        @if($entry->allowances)
                            @php($allowanceModel = \App\Models\Allowance::all())
                            @foreach($entry->allowances as $allowance)
                                @php($model = $allowanceModel->firstWhere('id', $allowance->allowance_id))
                                <tr>
                                    <td class="border-top-0 border-right">
                                        {{ $model->name }}
                                        @if($allowance->description)
                                            {{ $allowance->description }}
                                        @endif
                                    </td>
                                    <td class="text-right border-top-0 border-right">
                                        {{ format_money(strip_money_mask($allowance->amount)) }}
                                    </td>
                                    <td class="border-top-0">

                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        @if($entry->deductions)
                            @php($deductionModel = \App\Models\Deduction::all())
                            @foreach($entry->deductions as $deduction)
                                @php($model = $deductionModel->firstWhere('id', $deduction->deduction_id))
                                <tr>
                                    <td class="border-top-0 border-right">
                                        {{ $model->name }}
                                        @if($deduction->description)
                                            {{ $deduction->description }}
                                        @endif
                                    </td>
                                    <td class="border-top-0 border-right">

                                    </td>
                                    <td class="text-right border-top-0">
                                        ({{ format_money(strip_money_mask($deduction->amount)) }})
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <td class="border-top-0 border-right">&nbsp;</td>
                            <td class="border-top-0 border-right">&nbsp;</td>
                            <td class="border-top-0">&nbsp;</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="border-right">
                                Total
                            </td>
                            <td class="text-right border-right">
                                {{ $entry->formatted_total_earnings }}
                            </td>
                            <td class="text-right">
                                @if($entry->total_deductions)
                                    ({{ $entry->formatted_total_deductions }})
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th colspan="3">
                                <div class="d-flex justify-content-between">
                                    <span>
                                        NET PAY
                                    </span>
                                    <span>
                                        {{ $entry->formatted_net_pay }}
                                    </span>
                                </div>
                            </th>
                        </tr>
                        </tfoot>
                    </table>
                    <div class="mt-5" style="font-style: italic">
                        <strong>
                            Notes:
                        </strong>
                        {!! $entry->notes !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('after_styles')
    <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/crud.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/show.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css') }}">
    <style>
        @media print {
            body {
                font-size: 16pt;
            }

            .table, th, td, tr, .border-right {
                border-color: #000 !important;
            }
        }
    </style>
@endsection

@section('after_scripts')
    <script src="{{ asset('packages/backpack/crud/js/crud.js') }}"></script>
    <script src="{{ asset('packages/backpack/crud/js/show.js') }}"></script>
    <script src="{{ asset('packages/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        if (jQuery.ui) {
            var datepicker = $.fn.datepicker.noConflict();
            $.fn.bootstrapDP = datepicker;
        } else {
            $.fn.bootstrapDP = $.fn.datepicker;
        }
        $('.my-datepicker').bootstrapDP({
                               format: 'd MM yyyy'
                           })
                           .on('changeDate', function(e) {
                               console.log(e);
                           });
    </script>
@endsection
