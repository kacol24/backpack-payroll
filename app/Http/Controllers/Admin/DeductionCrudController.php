<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DeductionRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DeductionCrudController
 *
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DeductionCrudController extends AllowanceDeductionController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Deduction::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/deduction');
        CRUD::setEntityNameStrings('deduction', 'deductions');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(DeductionRequest::class);

        parent::setupCreateOperation();
    }
}
