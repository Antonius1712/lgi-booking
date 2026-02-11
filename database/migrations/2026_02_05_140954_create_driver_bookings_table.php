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
        Schema::create('driver_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->string('user_nik');
            $table->string('driver_nik');
            $table->string('status');

            $table->text('destination');

            $table->dateTime('scheduled_pickup_at');
            $table->date('scheduled_pickup_date');
            $table->time('scheduled_pickup_time');

            $table->dateTime('scheduled_end_at');
            $table->date('scheduled_end_date');
            $table->time('scheduled_end_time');

            $table->string('scheduled_time_slot');

            $table->bigInteger('scheduled_duration');
            $table->text('purpose_of_trip');

            $table->dateTime('actual_pickup_at')->nullable();
            $table->dateTime('actual_end_at')->nullable();
            $table->dateTime('user_confirmed_presence_at')->nullable();

            $table->integer('reminder_count')->default(0);
            $table->dateTime('last_reminder_sent_at')->nullable();

            $table->dateTime('extention_requested_at')->nullable();
            $table->bigInteger('extension_duration')->nullable();

            $table->bigInteger('extension_approved_by')->nullable();
            $table->dateTime('extension_approved_at')->nullable();

            $table->bigInteger('extension_rejected_by')->nullable();
            $table->dateTime('extension_rejected_at')->nullable();
            $table->text('extension_rejection_reason')->nullable();

            $table->bigInteger('cancelled_by')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancelation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_bookings');
    }
};
