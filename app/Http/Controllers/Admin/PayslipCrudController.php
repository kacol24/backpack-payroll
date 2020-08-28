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
        CRUD::setValidation(PayslipRequest::class);

        CRUD::field('period')->type('date_picker')
            ->label('Period');
        CRUD::field('employee_id')->type('select2')
            ->entity('employee')
            ->model(Employee::class)
            ->attribute('name')
            ->options(function ($query) {
                return $query->active()->get();
            })
            ->label('Employee');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
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
                    'name'      => 'allowance_id',
                    'type'      => 'select2',
                    'entity'    => 'allowances',
                    'attribute' => 'name',
                    'model'     => Allowance::class,
                    'label'     => 'Allowance',
                    'wrapper'   => ['class' => 'form-group col-6'],
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
                    'name'      => 'deduction_id',
                    'type'      => 'select2',
                    'entity'    => 'deductions',
                    'attribute' => 'name',
                    'model'     => Deduction::class,
                    'label'     => 'Deduction',
                    'wrapper'   => ['class' => 'form-group col-6'],
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
            ->label('Net Pay');
    }
}
