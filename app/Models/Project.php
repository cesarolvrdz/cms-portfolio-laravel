<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'title', 'description', 'tech', 'link', 'image', 'status'
    ];
    protected $casts = [
        'tech' => 'array',
    ];
    public function techTags()
    {
        return $this->belongsToMany(TechTag::class, 'project_tech_tags');
    }
}
