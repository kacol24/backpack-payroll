@extends(backpack_view('blank'))

@push('before_styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.2/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@5.3.2/main.min.css">
    <style>
        .overlay {
            position: absolute;
            background-color: rgba(255, 255, 255, .5);
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 999;
        }
    </style>
@endpush

@push('before_scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.2/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@5.3.2/main.global.min.js"></script>
@endpush
@push('after_scripts')
    <script>
        var calendarEl = document.getElementById('attendance_calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            eventDidMount: function(info) {
                $(info.el).tooltip({
                    title: info.event.extendedProps.description,
                    html: true
                });
                // var tooltip = new Tooltip(info.el, {
                //     title: info.event.extendedProps.description,
                //     placement: 'top',
                //     trigger: 'hover',
                //     container: 'body'
                // });
            },
            events: '{{ route('api.attendances.index') }}',
            loading: function(isLoading) {
                if (isLoading) {
                    $('.overlay').show();
                } else {
                    $('.overlay').hide();
                }
            }
        });
        calendar.render();
    </script>
    {{--    <script>--}}
    {{--        var calendarEl = document.getElementById('attendance_calendar');--}}
    {{--        var calendar = new FullCalendar.Calendar(calendarEl, {--}}
    {{--            initialView: 'timeGridWeek',--}}
    {{--            headerToolbar: {--}}
    {{--                left: 'prev,next today',--}}
    {{--                center: 'title',--}}
    {{--                right: 'timeGridDay,timeGridWeek,timeGridMonth'--}}
    {{--            },--}}
    {{--            views: {--}}
    {{--                timeGridMonth: {--}}
    {{--                    type: 'timeGrid',--}}
    {{--                    duration: {month: 1},--}}
    {{--                    buttonText: 'Month'--}}
    {{--                }--}}
    {{--            },--}}
    {{--            allDaySlot: false,--}}
    {{--            events: '{{ route('api.attendances.index') }}',--}}
    {{--            loading: function(isLoading) {--}}
    {{--                if (isLoading) {--}}
    {{--                    $('.overlay').show();--}}
    {{--                } else {--}}
    {{--                    $('.overlay').hide();--}}
    {{--                }--}}
    {{--            }--}}
    {{--        });--}}
    {{--        calendar.render();--}}
    {{--    </script>--}}
@endpush

@section('header')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize">
                Kalendar Absensi
            </span>
        </h2>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        asdjkl
                    </h4>
                </div>
                <div class="card-body position-relative">
                    <div id="attendance_calendar"></div>
                    <div class="overlay">
                        <i class="la la-refresh la-spin la-fw"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
