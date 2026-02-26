<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meeting_room_bookings', function (Blueprint $table) {
            // Add a plain index on meeting_room_id first so MySQL has an index to satisfy the FK.
            $table->index('meeting_room_id', 'mrb_meeting_room_id_idx');
        });

        Schema::table('meeting_room_bookings', function (Blueprint $table) {
            $table->dropUnique('mr_booking_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_room_bookings', function (Blueprint $table) {
            $table->unique(['meeting_room_id', 'booking_date', 'time_slot'], 'mr_booking_unique');
        });

        Schema::table('meeting_room_bookings', function (Blueprint $table) {
            $table->dropIndex('mrb_meeting_room_id_idx');
        });
    }
};
