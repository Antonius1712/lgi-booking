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
            $table->string('cancelled_by')->nullable()->after('guest_emails');
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            $table->text('cancelation_reason')->nullable()->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_room_bookings', function (Blueprint $table) {
            $table->dropColumn(['cancelled_by', 'cancelled_at', 'cancelation_reason']);
        });
    }
};
