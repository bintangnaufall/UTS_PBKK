<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class BookAuthors extends Model
{
    use HasUlids;
    
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $table = 'book_authors';

    protected $fillable = [
        'book_id',
        'author_id',
    ];

    
    protected function casts(): array
    {
        return [
            'book_id' => 'string',
            'author_id' => 'string',
        ];
    }

    public function book()
    {
        return $this->belongsTo(Books::class, 'book_id', 'book_id');
    }

    public function author()
    {
        return $this->belongsTo(Authors::class, 'author_id', 'author_id');
    }
}
