<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#prorateModal">
    Prorate Calculator
</button>

<div class="modal fade" id="prorateModal" tabindex="-1" data-backdrop="static" data-keyboard="false"
     aria-hidden="true" x-data="CalculatorApp()"
     x-on:calendar-changed.window="calendar_start = $event.detail.start_date; calendar_end = $event.detail.end_date; working_end = $event.detail.end_date"
     x-on:working-changed.window="working_start = $event.detail.start_date; working_end = $event.detail.end_date"
     x-on:salary-changed.window="salary = $event.detail.value">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Calculator</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <div class="container-fluid px-0">
                    <div class="row mx-n3">
                        <div class="col-12">
                            <fieldset>
                                <div class="form-group">
                                    <label for="">
                                        Gaji Pokok
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                Rp
                                                            </span>
                                        </div>
                                        <input type="tel" class="form-control text-right" data-money>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">
                                        Periode Gaji
                                    </label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control rangepicker"
                                               data-updates="calendar">
                                        <input type="hidden" name="calendar_start"
                                               x-model="calendar_start">
                                        <input type="hidden" name="calendar_end"
                                               x-model="calendar_end">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="la la-calendar"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">
                                        Periode Kerja
                                    </label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control rangepicker"
                                               data-updates="working">
                                        <input type="hidden" name="working_start"
                                               x-model="working_start">
                                        <input type="hidden" name="working_end"
                                               x-model="working_end">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="la la-calendar"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-12">
                            <fieldset>
                                <legend>Results</legend>
                                <dl>
                                    <dt>
                                        Total hari kerja:
                                    </dt>
                                    <dd>
                                        <span x-text="workingDelta()"></span>
                                        <template x-if="working_start && working_end">
                                            (<span x-text="moment(working_start).format('DD MMM YYYY')"></span>
                                            -
                                            <span x-text="moment(working_end).format('DD MMM YYYY')"></span>)
                                        </template>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>
                                        Total hari kalender:
                                    </dt>
                                    <dd>
                                        <span x-text="calendarDelta()"></span>
                                        <template x-if="calendar_start && calendar_end">
                                            (<span x-text="moment(calendar_start).format('DD MMM YYYY')"></span>
                                            -
                                            <span x-text="moment(calendar_end).format('DD MMM YYYY')"></span>)
                                        </template>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>
                                        Prorate gaji pokok:
                                    </dt>
                                    <dd>
                                        Rp<span x-text="number_format(prorate(), 0, ',', '.')"></span>
                                        <template x-if="prorate() > 0">
                                            (Dari gaji pokok:
                                            <span x-text="number_format(salary, 0, ',', '.')"></span>)
                                        </template>
                                    </dd>
                                </dl>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('after_styles')
    <script type="module" src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine-ie11.min.js" defer></script>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('packages/bootstrap-daterangepicker/daterangepicker.css') }}"/>
@endpush

@push('after_scripts')
    <script type="text/javascript" src="{{ asset('packages/moment/min/moment-with-locales.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('packages/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script>
        function number_format(number, decimals, dec_point, thousands_sep) {
            // Strip all characters but numerical ones.
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        $('[data-money]').toArray().forEach(function(field) {
            new Cleave(field, {
                numeral: true,
                numeralDecimalMark: ',',
                delimiter: '.',
                numeralDecimalScale: 0,
                swapHiddenInput: true,
                onValueChanged: function(e) {
                    var event = new CustomEvent('salary-changed', {
                        detail: {
                            value: e.target.rawValue
                        }
                    });
                    window.dispatchEvent(event);
                }
            });
        });

        $('#prorateModal').on('shown.bs.modal', function() {
            $('[autofocus]').trigger('focus');

            var $visibleInput = $('.rangepicker');

            $visibleInput.daterangepicker({
                autoUpdateInput: false
            });

            $visibleInput.on('apply.daterangepicker hide.daterangepicker', function(e, picker) {
                var $this = $(this);
                var updates = $this.data('updates');
                $this.val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

                var event = new CustomEvent(updates + '-changed', {
                    detail: {
                        start_date: picker.startDate.format('YYYY-MM-DD'),
                        end_date: picker.endDate.format('YYYY-MM-DD')
                    }
                });
                window.dispatchEvent(event);
            });
        });

        function CalculatorApp() {
            return {
                salary: null,
                calendar_start: null,
                calendar_end: null,
                working_start: null,
                working_end: null,

                calendarDelta: function() {
                    if (!this.calendar_start || !this.calendar_end) {
                        return '-';
                    }

                    return Math.abs(moment(this.calendar_start).diff(this.calendar_end, 'days')) + 1;
                },

                workingDelta: function() {
                    if (!this.working_start || !this.working_end) {
                        return '-';
                    }

                    return Math.abs(moment(this.working_start).diff(this.working_end, 'days')) + 1;
                },

                prorate: function() {
                    if (!this.salary || !this.workingDelta() || !this.calendarDelta()) {
                        return ' -';
                    }
                    return this.salary * this.workingDelta() / this.calendarDelta();
                }
            };
        }
    </script>
@endpush
