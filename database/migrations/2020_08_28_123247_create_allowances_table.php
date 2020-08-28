<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowances', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('description')->nullable();

            $table->boolean('is_active');
            $table->timestamps();
        });

        $allowances = [
            [
                'name' => 'Kerajinan',
            ],
            [
                'name' => 'Lembur',
            ],
            [
                'name' => 'Lembur weekend',
            ],
            [
                'name' => 'Bonus target',
            ],
        ];

        \DB::table('allowances')->insertOrIgnore($allowances);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allowances');
    }
}
