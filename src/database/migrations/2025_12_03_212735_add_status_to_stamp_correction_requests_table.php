<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToStampCorrectionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('stamp_correction_requests')) {
            Schema::table('stamp_correction_requests', function (Blueprint $table) {
                
                if (!Schema::hasColumn('stamp_correction_requests', 'status')) {
                   
                    $table->string('status')->default('承認待ち')->after('attendance_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('stamp_correction_requests')) {
            Schema::table('stamp_correction_requests', function (Blueprint $table) {
                if (Schema::hasColumn('stamp_correction_requests', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
}
