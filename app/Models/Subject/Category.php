<?php
/**
 * 课程分类模型
 * @author Dracowyn
 * @since 2024-01-06 16:29
 */

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'subject_category';

    public $timestamps = false;

    protected $guarded = [];
}
