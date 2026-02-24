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
        Schema::create('driver_booking_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_booking_id');
            $table->string('user_nik');
            $table->unsignedTinyInteger('rating');
            $table->json('feedback_tag_ids')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('driver_booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_booking_feedbacks');
    }
};
