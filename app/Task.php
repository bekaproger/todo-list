<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
    	'task', 'user_id'
    ];

    public function taskUser()
    {
    	return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
