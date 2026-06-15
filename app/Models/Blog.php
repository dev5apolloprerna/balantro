<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blog';
    protected $primaryKey = 'blog_id';

    protected $fillable = [
        'title',
        'category_id',
        'slugname',
        'description',
        'image',
        'metaTitle',
        'metaKeyword',
        'metaDescription',
        'head',
        'body',
        'iStatus',
        'isDelete',
        'strIP',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}
