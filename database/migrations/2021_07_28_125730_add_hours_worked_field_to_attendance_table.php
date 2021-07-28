<?php

use App\Models\Attendance;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHoursWorkedFieldToAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::beginTransaction();

        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedFloat('hours_worked')->default(0)->after('end_at');
        });

        $originalData = Attendance::whereNotNull('end_at')->get();

        foreach ($originalData as $updateMe) {
            $updateMe->update([
                'hours_worked' => $updateMe->real_hours_worked,
            ]);
        }
        \DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['hours_worked']);
        });
    }
}
