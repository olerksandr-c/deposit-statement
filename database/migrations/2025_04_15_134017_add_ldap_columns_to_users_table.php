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
        Schema::table('users', function (Blueprint $table) {
            // Добавляем поле для LDAP GUID, делаем его уникальным и индексируемым
            $table->string('guid')->unique()->nullable()->after('id');
            // Добавляем поле для домена (если нужно, например, при работе с несколькими доменами)
            $table->string('domain')->nullable()->after('guid');
            // Делаем поле пароля необязательным, т.к. он не будет храниться для LDAP пользователей
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // При откате делаем пароль снова обязательным (если он был таким)
            // Внимание: Это может вызвать проблемы, если у вас уже есть LDAP пользователи без пароля в БД!
            $table->string('password')->nullable(false)->change();
            $table->dropColumn(['guid', 'domain']);
        });
    }
};
