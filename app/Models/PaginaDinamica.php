<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaginaDinamica extends Model
{
    protected $table = 'paginas_dinamicas';
    protected $primaryKey = 'idpagina';
    public $timestamps = false;

    protected $fillable = [
        'slug',
        'titulo',
        'descripcion',
        'idcampania',
        'api_token',
        'estado_general',
        'estado'
    ];

    protected $hidden = [
        'userinsert',
        'dateinsert',
        'userupdate',
        'dateupdate',
    ];

    protected $appends = [
        'isactive',
        'statename',
    ];

    public function getIsactiveAttribute()
    {
        return $this->estado === 'A';
    }

    public function getStatenameAttribute()
    {
        return $this->estado === 'A' ? 'Activo' : 'Inactivo';
    }

    public function campania()
    {
        return $this->belongsTo(Campania::class, 'idcampania', 'idcampania');
    }
}
