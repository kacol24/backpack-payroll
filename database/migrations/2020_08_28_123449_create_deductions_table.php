<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('description')->nullable();

            $table->boolean('is_active');
            $table->timestamps();
        });

        $deductions = [
            [
                'name' => 'Alfa',
            ],
            [
                'name' => 'Izin',
            ],
            [
                'name' => 'Libur weekend',
            ],
            [
                'name' => 'Terlambat',
            ],
        ];

        \DB::table('deductions')->insertOrIgnore($deductions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deductions');
    }
}
