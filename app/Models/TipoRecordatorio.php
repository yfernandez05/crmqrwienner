<?php

namespace App\Models;

use App\Util\RuleManager;
use Illuminate\Database\Eloquent\Model;

class TipoRecordatorio extends Model
{
    protected $table = 'tiporecordatorios';
    protected $primaryKey ='idtiporecordatorio';
    public $timestamps = false;

    protected $fillable = [
        'tiporecordatorio',
        'backgroundColor',
        'textColor',
        'estado',
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

    public function getIsactiveAttribute(){
        return RuleManager::getIsActive($this->estado);
    }

    public function getStatenameAttribute(){
        return RuleManager::getStateName($this->estado);
    }
}
