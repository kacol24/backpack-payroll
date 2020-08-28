<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllowancePayslipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowance_payslip', function (Blueprint $table) {
            $table->foreignId('allowance_id');
            $table->foreignId('payslip_id');
            $table->string('description')->nullable();
            $table->unsignedInteger('amount');

            $table->foreign('allowance_id')->references('id')->on('allowances');
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
        Schema::dropIfExists('allowance_payslip');
    }
}
