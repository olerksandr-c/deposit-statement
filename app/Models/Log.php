<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    // Указываем таблицу
    protected $table = 'logs';

    // Указываем заполняемые поля
    protected $fillable = [
        'user_id',
        'log_type',
        'message',
        'is_archived',
    ];

    // Ожидаем, что поле is_archived будет булевым значением
    protected $casts = [
        'is_archived' => 'boolean',
    ];

    // Связь с моделью User (если есть)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
