<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'date', 'vote'];
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_exams', 'exam_id', 'user_id');
    }
}
