<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Books extends Model
{
    use HasUlids;
    
    protected $primaryKey = 'book_id';
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $table = 'books';

    protected $fillable = [
        'title',
        'isbn',
        'publisher',
        'year_published',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'string',
            'isbn' => 'string',
            'publisher' => 'string',
            'year_published' => 'string',
            'stock' => 'integer',
        ];
    }

    public function loans()
    {
        return $this->hasMany(Loans::class, 'book_id', 'book_id');
    }

    public function book_author()
    {
        return $this->hasMany(BookAuthors::class, 'book_id', 'book_id');
    }
}
