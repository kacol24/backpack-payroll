<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionPayslipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deduction_payslip', function (Blueprint $table) {
            $table->foreignId('deduction_id');
            $table->foreignId('payslip_id');
            $table->string('description')->nullable();
            $table->unsignedInteger('amount');

            $table->foreign('deduction_id')->references('id')->on('deductions');
            $table->foreign('payslip_id')->references('id')->on('payslips');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deduction_payslip');
    }
}
