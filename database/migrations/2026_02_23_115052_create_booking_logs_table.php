<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_logs', function (Blueprint $table) {
            $table->id();
            $table->string('loggable_type');          // DriverBooking or MeetingRoomBooking
            $table->unsignedBigInteger('loggable_id');
            $table->string('action');                  // confirmed, cancelled, driver_changed, extended, etc.
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->json('payload')->nullable();       // any extra data (old driver, reason, hours, etc.)
            $table->string('performed_by')->nullable(); // NIK of admin/driver who did it
            $table->string('performed_by_role')->nullable(); // admin, driver, system
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['loggable_type', 'loggable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_logs');
    }
};
