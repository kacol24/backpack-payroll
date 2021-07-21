<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AttendanceRequest;
use App\Models\Employee;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

/**
 * Class AttendanceCrudController
 *
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AttendanceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Attendance::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/attendance');
        CRUD::setEntityNameStrings('attendance', 'attendances');

        $this->crud->setListView('attendance.list');
    }

    public function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->setupListOperation();
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addFilter([
            'type'  => 'date_range',
            'name'  => 'shift_date',
            'label' => 'Shift Date',
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'shift_date', '>=', Carbon::parse($dates->from)->startOfDay());
                $this->crud->addClause('where', 'shift_date', '<=', Carbon::parse($dates->to)->endOfDay());
            });

        $this->crud->addFilter([
            'name'  => 'employee_id',
            'type'  => 'select2_multiple',
            'label' => 'Employee',
        ], function () {
            return Employee::active()->get()->pluck('name', 'id')->toArray();
        }, function ($values) { // if the filter is active
            $this->crud->addClause('whereIn', 'employee_id', json_decode($values));
        });

        CRUD::column('employee')
            ->type('relationship')
            ->label('Employee');
        CRUD::column('start_at')
            ->type('view')
            ->view('attendance.columns.clock_in')
            ->label('Clock In');
        CRUD::column('end_at')
            ->type('view')
            ->view('attendance.columns.clock_out')
            ->label('Clock Out');
        CRUD::column('hours_worked')
            ->type('number')
            ->label('Hours Worked');
        CRUD::column('comment')
            ->type('text')
            ->label('Comment');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(AttendanceRequest::class);

        CRUD::field('selfie_in')
            ->label('Selfie In')
            ->type('upload')
            ->upload(true);
        CRUD::field('selfie_out')
            ->label('Selfie Out')
            ->type('upload')
            ->upload(true);
        CRUD::field('employee_id')->type('select2')
            ->entity('employee')
            ->model(Employee::class)
            ->attribute('name')
            ->options(function ($query) {
                return $query->active()->get();
            })
            ->label('Employee');
        CRUD::field('start_at')
            ->type('datetime_picker')
            ->label('Clock In');
        CRUD::field('end_at')
            ->type('datetime_picker')
            ->label('Clock Out');
        CRUD::field('comment')
            ->type('textarea')
            ->label('Comment');
        CRUD::field('shift_date')
            ->type('hidden')
            ->value(now()->format('Y-m-d'));
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
