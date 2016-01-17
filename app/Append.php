<?php

namespace App;

class Append extends Model
{
    protected $fillable = ['topic_id', 'content'];

    public function topic()
    {
        return $this->belongsTo('Topic');
    }
}
