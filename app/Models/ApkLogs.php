<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApkLogs extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $table = 'apk_logs';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'version_type', 'version_number', 'file_name'
    ];
}
