<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Loans extends Model
{
    use HasUlids;
    
    protected $primaryKey = 'loan_id';
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $table = 'loans';

    protected $fillable = [
        'user_id',
        'book_id',
    ];

    
    protected function casts(): array
    {
        return [
            'user_id' => 'string',
            'book_id' => 'string',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relasi ke Book
    public function book()
    {
        return $this->belongsTo(Books::class, 'book_id', 'book_id');
    }
}
