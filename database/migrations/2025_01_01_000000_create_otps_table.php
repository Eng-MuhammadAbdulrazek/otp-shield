<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->morphs('otpable');
            $table->string('secret');
            $table->enum('type', ['totp', 'hotp'])->default(config('otp-shield.default_otp_type'));
            $table->unsignedInteger('digits')->default(config('otp-shield.digits'));
            $table->unsignedInteger('period')->default(config('otp-shield.period'));
            $table->unsignedInteger('counter')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('otps');
    }
};
