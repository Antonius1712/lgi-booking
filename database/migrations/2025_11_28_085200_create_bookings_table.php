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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_room_id')->constrained();
            $table->string('nik');
            $table->date('booking_date');
            $table->string('time_slot', 50);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('confirmed');
            $table->string('title')->nullable();
            $table->string('event_url')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->enum('calendar_type', ['Meeting', 'Interview'])->default('Meeting');
            $table->json('guest_emails')->nullable(); // ← NEW: Store invited guests
            $table->timestamps();

            $table->unique(['meeting_room_id', 'booking_date', 'time_slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
