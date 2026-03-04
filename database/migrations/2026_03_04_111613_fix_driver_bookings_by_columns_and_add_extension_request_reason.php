<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_bookings', function (Blueprint $table) {
            $table->string('extension_approved_by')->nullable()->change();
            $table->string('extension_rejected_by')->nullable()->change();
            $table->string('cancelled_by')->nullable()->change();
            $table->text('extension_request_reason')->nullable()->after('extension_duration');
        });
    }

    public function down(): void
    {
        Schema::table('driver_bookings', function (Blueprint $table) {
            $table->bigInteger('extension_approved_by')->nullable()->change();
            $table->bigInteger('extension_rejected_by')->nullable()->change();
            $table->bigInteger('cancelled_by')->nullable()->change();
            $table->dropColumn('extension_request_reason');
        });
    }
};
