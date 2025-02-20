<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'content',
    'user_id'
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'user_id' => 'integer',
  ];

  /**
   * Relationship to task (polymorphic relationship).
   * 
   * @return \Illuminate\Database\Eloquent\Relations\MorphTo
   */
  public function commentable()
  {
    return $this->morphTo();
  }

  /**
   * Relationship to user who write the comment.
   * 
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
