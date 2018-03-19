<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryPassword extends Model
{
    /**
     * [$fillable description]
     * @var [type]
     */
    protected $fillable = ['user_id','password'];

    /**
     * The user_id belongs to user
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
