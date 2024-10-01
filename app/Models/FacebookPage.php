<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookPage extends Model
{
    use HasFactory;

    protected $table = 'page_campaigns'; // Altere para o nome da tabela se necessário

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'page_id',
        'last_activity_campaign'
    ];

}
