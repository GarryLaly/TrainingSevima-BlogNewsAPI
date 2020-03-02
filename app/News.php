<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class News extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'photo',
        'content',
    ];

    public function getPhoto()
    {
        if ($this->photo) {
            return asset('uploads/' . $this->photo);
        }

        return asset('uploads/default.png');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
