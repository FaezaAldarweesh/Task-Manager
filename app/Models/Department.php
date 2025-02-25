<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      'name'
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
     * Define the one-to-many relationship between users and department.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function users()
    {
      return $this->hasMany(User::class);
    }
}
