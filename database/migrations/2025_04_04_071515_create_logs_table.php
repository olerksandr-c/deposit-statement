<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // id пользователя
            $table->enum('log_type', ['error', 'info', 'warning', 'debug'])->default('info'); // тип сообщения
            $table->text('message'); // само сообщение
            $table->boolean('is_archived')->default(false); // флаг архивирования
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
