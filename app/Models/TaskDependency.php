<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskDependency extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'task_id',
    'depends_on',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    //
  ];

  /**
   * Relationship to the task that depends on another task.
   * 
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function task()
  {
    return $this->belongsTo(Task::class, 'task_id');
  }

  /**
   * Relationship to the task that this task depends on.
   * 
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function dependsOnTask()
  {
    return $this->belongsTo(Task::class, 'depends_on');
  }
}
