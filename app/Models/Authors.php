<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Authors extends Model
{
    use HasUlids;
    
    protected $primaryKey = 'author_id';
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $table = 'authors';
    
    protected $fillable = [
        'name',
        'nationality',
        'birthdate',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'nationality' => 'string',
            'birthdate' => 'string',
        ];
    }

    public function book_author()
    {
        return $this->hasMany(BookAuthors::class, 'author_id', 'author_id');
    }
}
