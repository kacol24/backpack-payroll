<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EmployeeRequest;
use App\Models\Allowance;
use App\Models\Deduction;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EmployeeCrudController
 *
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EmployeeCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Employee::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/employee');
        CRUD::setEntityNameStrings('employee', 'employees');

        $this->crud->addButtonFromModelFunction('line', 'attendance_button', 'attendanceButtons', 'beginning');
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
        CRUD::setValidation(EmployeeRequest::class);

        CRUD::field('employee_number')->type('text')->label('Nomor Induk Karyawan');
        CRUD::field('is_active')->type('checkbox')->label('Active?')->default(true);
        CRUD::field('name')->type('text')->label('Name');
        CRUD::field('salary')->type('money')->label('Salary')->prefix('Rp');

        CRUD::field('start_date')->type('date_picker')->label('Start Date');
        CRUD::field('bio')->type('textarea')->label('Bio');

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
