<?php

namespace App\Models;

class GroupUser extends Model
{
    //protected $table = 'group_users';
    // protected $fillable = [
    //     'group_id',
    //     'user_id'
    // ];
    public $timestamps = true;

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
