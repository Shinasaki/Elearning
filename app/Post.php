<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'classroom_id', 'type', 'detail'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function classroom()
    {
        return $this->belongsTo('App\Classroom');
    }

    public function assignment()
    {
        return $this->hasOne('App\Assignment');
    }

    public function attachments()
    {
        return $this->belongsToMany('App\FilesAttachment');
    }
}
