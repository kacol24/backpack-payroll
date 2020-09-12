<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('employee') }}'><i class='nav-icon la la-question'></i> Employees</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('allowance') }}'><i class='nav-icon la la-question'></i> Allowances</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('deduction') }}'><i class='nav-icon la la-question'></i> Deductions</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('payslip') }}'><i class='nav-icon la la-question'></i> Payslips</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('attendance') }}'><i class='nav-icon la la-question'></i> Attendances</a></li>
<li class="nav-item">
    <a href="{{ route('report.calendar') }}" class="nav-link">
        <i class="nav-icon la la-chart-area"></i>
        Kalender Absensi
    </a>
</li>
