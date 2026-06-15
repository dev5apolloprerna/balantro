<?php

namespace App\Models;

class DocumentComment extends Model
{
    const TYPE_QUERY = 'query';
    const TYPE_COMMENT = 'comment';

    public const TYPE_QUERY_RAISED   = 2;
    public const TYPE_QUERY_RESOLVED = 3;

    protected $fillable = [
        'document_id',
        'commented_by_id',
        'commented_by_type',
        'comment',
        'comment_type'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function commentedBy()
    {
        return $this->morphTo();
    }
}
