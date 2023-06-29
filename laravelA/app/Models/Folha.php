<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pessoa;

class Folha extends Model
{
    protected $table = 'folhas';

    protected $guarded = ["id"];

    protected $hidden = ['created_at', 'updated_at'];

    public function pessoa()
    {
        return $this->hasOne(Pessoa::class, "id", "id_pessoa");
    }
}
