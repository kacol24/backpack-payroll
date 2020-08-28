<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayslipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id');
            $table->string('name')->nullable();
            $table->date('period');

            $table->unsignedInteger('gross_pay')->default(0);
            $table->unsignedInteger('total_allowances')->default(0);
            $table->unsignedInteger('total_deductions')->default(0);
            $table->unsignedInteger('net_pay')->default(0);

            $table->longText('allowances')->nullable();
            $table->longText('deductions')->nullable();

            $table->timestamps();
            $table->timestamp('paid_at')->nullable();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->unique(['employee_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payslips');
    }
}
