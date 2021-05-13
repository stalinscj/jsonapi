<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['title', 'content', 'category_id', 'user_id'];

    /**
     * The allowed fields to sort.
     *
     * @var string[]
     */
    public $allowedSorts = ['title', 'content'];
}
