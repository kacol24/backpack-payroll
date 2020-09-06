<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReorderFieldsToAllowancesAndDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allowances', function (Blueprint $table) {
            $table->integer('parent_id')->default(0)->nullable()->index()
                  ->after('is_active');
            $table->integer('lft')->default(0)->index()
                  ->after('parent_id');
            $table->integer('rgt')->default(0)->index()
                  ->after('lft');
            $table->integer('depth')->default(0)
                  ->after('rgt');
        });
        Schema::table('deductions', function (Blueprint $table) {
            $table->integer('parent_id')->default(0)->nullable()->index()
                  ->after('is_active');
            $table->integer('lft')->default(0)->index()
                  ->after('parent_id');
            $table->integer('rgt')->default(0)->index()
                  ->after('lft');
            $table->integer('depth')->default(0)
                  ->after('rgt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allowances', function (Blueprint $table) {
            $table->dropColumn([
                'parent_id',
                'lft',
                'rgt',
                'depth',
            ]);
        });
    }
}
