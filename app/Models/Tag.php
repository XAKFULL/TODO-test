<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class);
    }

    public static function findOrCreate(array $names): array
    {
        return collect($names)
            ->map(function ($name) {
                return static::firstOrCreate(['name' => trim($name)]);
            })
            ->pluck('id')
            ->all();

    }
}
