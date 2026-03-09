<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use SoftDeletes;
    protected $fillable = ['nome', 'hackathon_id', 'lider_id', 'codigo', 'imagem'];

    public function hackathon()
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function lider()
    {
        return $this->belongsTo(User::class, 'lider_id');
    }

    public function membros()
    {
        return $this->belongsToMany(User::class);
    }
}
