<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PayslipRequest;
use App\Models\Allowance;
use App\Models\Deduction;
use App\Models\Employee;
use App\Models\Payslip;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

/**
 * Class PayslipCrudController
 *
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PayslipCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Payslip::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/payslip');
        CRUD::setEntityNameStrings('payslip', 'payslips');

        $this->crud->setShowView('payslip.show');
        $this->crud->setListView('payslip.list');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $this->crud->hasAccessOrFail('show');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry
        $this->data['entry'] = $entry = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $entry->name . '-' . strtoupper($entry->employee->name);
        $this->data['forceTitle'] = true;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getShowView(), $this->data);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        $data = $this->crud->getStrippedSaveRequest();
        $data['gross_pay'] = Employee::find($data['employee_id'])->salary;
        $data['net_pay'] = $data['gross_pay'];

        // insert item in the db
        $item = $this->crud->create($data);
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    /**
     * Update the specified resource in the database.
     *
     * @return Response
     */
    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        $data = $this->crud->getStrippedSaveRequest();

        $allowances = $data['allowances'];
        $totalAllowances = json_decode($allowances, true);
        $data['total_allowances'] = array_reduce($totalAllowances, function ($carry, $allowance) {
            $carry += strip_money_mask($allowance['amount']);

            return $carry;
        }, $initial = 0);

        $deductions = $data['deductions'];
        $totalDeductions = json_decode($deductions, true);
        $data['total_deductions'] = array_reduce($totalDeductions, function ($carry, $deduction) {
            $carry += strip_money_mask($deduction['amount']);

            return $carry;
        }, $initial = 0);

        $data['net_pay'] = strip_money_mask($data['gross_pay']) + $data['total_allowances'] - $data['total_deductions'];

        // update the row in the db
        $item = $this->crud->update(
            $request->get($this->crud->model->getKeyName()),
            $data
        );
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // select2_multiple filter
        $this->crud->addFilter([
            'name'  => 'year',
            'type'  => 'select2_multiple',
            'label' => 'Year',
        ], function () {
            return Payslip::all()->groupBy(function ($value) {
                return $value->period->year;
            })->map(function ($value, $index) {
                return $index;
            })->toArray();
        }, function ($values) { // if the filter is active
            foreach (json_decode($values) as $index => $year) {
                if ($index == 0) {
                    $this->crud->addClause('whereYear', 'period', $year);
                } else {
                    $this->crud->addClause('orWhereYear', 'period', $year);
                }
            }
        });

        // select2_multiple filter
        $this->crud->addFilter([
            'name'  => 'month',
            'type'  => 'select2_multiple',
            'label' => 'Month',
        ], function () {
            return Payslip::all()->groupBy(function ($value) {
                return $value->period->month;
            })->map(function ($value, $index) {
                return Carbon::createFromDate(null, $index)->translatedFormat('F');
            })->toArray();
        }, function ($values) {
            foreach (json_decode($values) as $index => $month) {
                if ($index == 0) {
                    $this->crud->addClause('whereMonth', 'period', $month);
                } else {
                    $this->crud->addClause('orWhereMonth', 'period', $month);
                }
            }
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

        CRUD::column('name')->label('No. Slip');
        CRUD::column('period')->label('Period')
            ->format('MMMM YYYY')
            ->type('date');
        CRUD::column('employee')->label('Employee')
            ->type('relationship');
        CRUD::column('gross_pay')->label('Gross Pay')
            ->type('number')
            ->thousands_sep('.')
            ->prefix('Rp');
        CRUD::column('total_allowances')->label('Allowances')
            ->type('number')
            ->thousands_sep('.')
            ->prefix('Rp');
        CRUD::column('total_deductions')->label('Deductions')
            ->type('number')
            ->thousands_sep('.')
            ->prefix('Rp');
        CRUD::column('net_pay')->label('Net Pay')
            ->type('number')
            ->thousands_sep('.')
            ->prefix('Rp');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->crud->removeSaveActions(['save_and_new', 'save_and_back', 'save_and_preview']);

        CRUD::setValidation(PayslipRequest::class);

        CRUD::field('name')->label('No. Slip');
        CRUD::field('employee_id')->type('select2')
            ->entity('employee')
            ->model(Employee::class)
            ->attribute('name')
            ->options(function ($query) {
                return $query->active()->get();
            })
            ->label('Employee');
        CRUD::field('period')->type('date_picker')
            ->date_picker_options([
                'format'      => 'MM yyyy',
                'minViewMode' => 'year',
            ])
            ->label('Period');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->crud->removeSaveActions(['save_and_new', 'save_and_back']);
        CRUD::setUpdateContentClass('col-md-12');

        CRUD::field('employee_id')->type('select2')
            ->entity('employee')
            ->model(Employee::class)
            ->attribute('name')
            ->options(function ($query) {
                return $query->active()->get();
            })
            ->attributes([
                'disabled' => 'disabled',
            ])
            ->wrapper([
                'class' => 'form-group col-sm-6',
            ])
            ->label('Employee');
        CRUD::field('period')->type('date_picker')
            ->attributes([
                'disabled' => 'disabled',
            ])
            ->wrapper([
                'class' => 'form-group col-sm-6',
            ])
            ->label('Period');

        CRUD::field('name')->label('No. Slip')
            ->wrapper([
                'class' => 'form-group col-sm-6',
            ]);
        CRUD::field('paid_at')->label('Paid At')
            ->type('date_picker')
            ->date_picker_options([
                'todayBtn' => 'linked',
            ])
            ->wrapper([
                'class' => 'form-group col-sm-6',
            ]);

        CRUD::field('gross_pay')->type('money')
            ->prefix('Rp')
            ->suffix(view('partials.prorate_calculator'))
            ->attributes([
                'id' => 'gross_pay'
            ])
            ->label('Gross Pay');

        CRUD::addField([   // repeatable
            'name'           => 'allowances',
            'label'          => 'Allowances',
            'type'           => 'repeatable',
            'wrapper'        => [
                'class' => 'form-group col-sm-6',
            ],
            'fields'         => [
                [
                    'name'    => 'allowance_id',
                    'label'   => "Allowance",
                    'type'    => 'select2_from_array',
                    'options' => Allowance::ordered()->pluck('name', 'id'),
                    'wrapper' => ['class' => 'form-group col-6'],
                ],
                [
                    'name'    => 'amount',
                    'type'    => 'money',
                    'label'   => 'Amount',
                    'prefix'  => 'Rp',
                    'wrapper' => ['class' => 'form-group col-6'],
                ],
                [
                    'name'    => 'description',
                    'wrapper' => ['class' => 'form-group col-12'],
                ],
            ],
            'new_item_label' => 'Add Allowance',
        ]);
        CRUD::addField([   // repeatable
            'name'           => 'deductions',
            'label'          => 'Deductions',
            'type'           => 'repeatable',
            'wrapper'        => [
                'class' => 'form-group col-sm-6',
            ],
            'fields'         => [
                [
                    'name'    => 'deduction_id',
                    'label'   => "Deduction",
                    'type'    => 'select2_from_array',
                    'options' => Deduction::ordered()->pluck('name', 'id'),
                    'wrapper' => ['class' => 'form-group col-6'],
                ],
                [
                    'name'    => 'amount',
                    'type'    => 'money',
                    'label'   => 'Amount',
                    'prefix'  => 'Rp',
                    'wrapper' => ['class' => 'form-group col-6'],
                ],
                [
                    'name'    => 'description',
                    'wrapper' => ['class' => 'form-group col-12'],
                ],
            ],
            'new_item_label' => 'Add Deduction',
        ]);

        CRUD::field('notes')->label('Notes')
            ->type('wysiwyg');

        CRUD::field('net_pay')->type('money')
            ->attributes([
                'disabled' => 'disabled',
            ])
            ->prefix('Rp')
            ->label('Net Pay');
    }
}
