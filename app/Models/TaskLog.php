<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    protected $fillable = ['task_name', 'status', 'message', 'run_at'];
}

