<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'title',
    'description',
    'status',
    'priority',
    'due_date',
    'assigned_to',
    'created_by',
  ];
  protected $dates = ['deleted_at'];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'due_date'    => 'date',
    'assigned_to' => 'integer',
    'created_by'  => 'integer',
  ];

  /**
   * Relationship to the assigned user.
   * 
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function assignedTo()
  {
    return $this->belongsTo(User::class, 'assigned_to');
  }

  /**
   * Relationship to the user who created the task.
   * 
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by');
  }


  /**
   * Relationship to task's comments (polymorphic relationship).
   * 
   * @return \Illuminate\Database\Eloquent\Relations\MorphMany
   */
  public function comments()
  {
    return $this->morphMany(Comment::class, 'commentable');
  }

  /**
   * Relationship to task's attachments (polymorphic relationship).
   * 
   * @return \Illuminate\Database\Eloquent\Relations\MorphMany
   */
  public function attachments()
  {
    return $this->morphMany(Attachment::class, 'attachable');
  }

  /**
   * Get all status updates for the task.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function statusUpdates()
  {
    return $this->hasMany(TaskStatusUpdate::class);
  }

  /**
   * Scope for filtering by status.
   * 
   * @param mixed $query
   * @param mixed $status
   * @return mixed
   */
  public function scopeStatus($query, $status)
  {
    if ($status) {
      return $query->where('status', $status);
    }
    return $query;
  }

  /**
   * Scope for filtering by assigned user.
   * 
   * @param mixed $query
   * @param mixed $assignedTo
   * @return mixed
   */
  public function scopeAssignedTo($query, $assignedTo)
  {
    if ($assignedTo) {
      return $query->where('assigned_to', $assignedTo);
    }
    return $query;
  }

  /**
   * Scope for filtering by due date.
   * 
   * @param mixed $query
   * @param mixed $dueDate
   * @return mixed
   */
  public function scopeDueDate($query, $dueDate)
  {
    if ($dueDate) {
      return $query->whereDate('due_date', $dueDate);
    }
    return $query;
  }

  /**
   * Scope for filtering by priority.
   * 
   * @param mixed $query
   * @param mixed $priority
   * @return mixed
   */
  public function scopePriority($query, $priority)
  {
    if ($priority) {
      return $query->where('priority', $priority);
    }
    return $query;
  }

}
