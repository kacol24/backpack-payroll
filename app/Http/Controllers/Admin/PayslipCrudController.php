<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PayslipRequest;
use App\Models\Allowance;
use App\Models\Deduction;
use App\Models\Employee;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

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
            $carry += $deduction['amount'];

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
        CRUD::setFromDb(); // columns
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
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

        CRUD::field('employee_id')->type('select2')
            ->entity('employee')
            ->model(Employee::class)
            ->attribute('name')
            ->options(function ($query) {
                return $query->active()->get();
            })
            ->wrapper([
                'class' => 'form-group col-sm-6',
            ])
            ->label('Employee');
        CRUD::field('period')->type('date_picker')
            ->wrapper([
                'class' => 'form-group col-sm-6',
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
        $this->crud->removeSaveActions(['save_and_new', 'save_and_back', 'save_and_preview']);
        CRUD::setUpdateContentClass('col-md-12');
        CRUD::field('period')->type('date_picker')
            ->attributes([
                'disabled' => 'disabled',
            ])
            ->wrapper([
                'class' => 'form-group col-sm-6',
            ])
            ->label('Period');
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

        CRUD::field('gross_pay')->type('money')
            ->prefix('Rp')
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
                    'options' => Allowance::pluck('name', 'id'),
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
                    'options' => Deduction::pluck('name', 'id'),
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

        CRUD::field('net_pay')->type('money')
            ->attributes([
                'disabled' => 'disabled',
            ])
            ->prefix('Rp')
            ->label('Net Pay');
    }
}
