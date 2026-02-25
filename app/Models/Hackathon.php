<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hackathon extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'data_inicio',
        'data_fim',
        'banner',
        'status',
        'winner_group_id',
        'finalized_at',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    /**
     * Grupo vencedor do hackathon
     */
    public function winnerGroup()
    {
        return $this->belongsTo(Grupo::class, 'winner_group_id');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    /**
     * Registros de presença do hackathon
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * Escopo para hackathons ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Escopo para hackathons finalizados
     */
    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalized');
    }

    /**
     * Verifica se o hackathon está finalizado
     */
    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }

    /**
     * Verifica se o hackathon está ativo
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
