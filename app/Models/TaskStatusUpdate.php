<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskStatusUpdate extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'assigned_to', 
    'created_by',
    'task_id',
    'user_id', //Who made the edit
    'previous_status',
    'new_status',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'task_id' => 'integer',
    'user_id' => 'integer',
  ];

  /**
   * Get the task associated with this status update.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function task()
  {
    return $this->belongsTo(Task::class);
  }

  /**
   * Get the user who made this status update.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
