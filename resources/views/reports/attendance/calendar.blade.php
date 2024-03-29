@extends(backpack_view('blank'))

@push('before_styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.2/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@5.3.2/main.min.css">
    <style>
        .overlay {
            position: absolute;
            background-color: rgba(255, 255, 255, .9);
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fc-footer-toolbar {
            display: none !important;
        }

        @media (max-width: 575.98px) {
            .fc-header-toolbar {
                flex-direction: column;
                margin-bottom: 0 !important;
            }

            .fc-header-toolbar .fc-toolbar-chunk {
                margin-bottom: 1rem;
            }

            .fc-header-toolbar .fc-toolbar-chunk:last-child {
                display: none;
            }

            .fc-footer-toolbar {
                display: flex !important;
            }
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
            views: {
                listWeek: {buttonText: 'list week'},
                listMonth: {buttonText: 'list month'}
            },
            firstDay: 1,
            businessHours: {
                startTime: '12:00',
                endTime: '22:00'
            },
            headerToolbar: {
                center: 'dayGridMonth,timeGridWeek,listMonth,listWeek',
                left: 'title',
                right: 'prev,next'
            },
            footerToolbar: {
                left: 'prev',
                right: 'next'
            },
            initialView: 'dayGridMonth',
            eventDidMount: function(info) {
                $(info.el).tooltip({
                    title: info.event.extendedProps.description,
                    html: true
                });
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
@endpush

@section('header')
    <div class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-md-auto">
                <h2 class="text-capitalize">
                    Kalendar Absensi
                </h2>
            </div>
            <div class="col-md-auto text-right">
                @include('partials.prorate_calculator')
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body position-relative">
                    <div id="attendance_calendar"></div>
                    <div class="overlay">
                        <i class="la la-refresh la-spin la-fw la-2x la-spin"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
