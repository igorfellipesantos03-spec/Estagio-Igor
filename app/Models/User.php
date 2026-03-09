<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Grupo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'matricula',
        'tipo',
        'avatar',
        'current_xp',
        'current_level',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tipo' => 'string',
    ];

    // Tipos de usuário permitidos
    protected $enums = [
        'tipo' => ['aluno', 'professor', 'adm'],
    ];

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_user', 'user_id', 'grupo_id')
            ->withTimestamps();
    }

    public function gruposLiderados()
    {
        return $this->hasMany(Grupo::class, 'lider_id')
            ->with('membros', 'hackathon');
    }

    /**
     * Registros de presença do usuário
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
