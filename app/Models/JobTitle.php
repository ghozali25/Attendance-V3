<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class JobTitle extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'name',
        'level', // Deprecated, use job_level_id
        'job_level_id',
        'division_id',
    ];

    public function jobLevel()
    {
        return $this->belongsTo(JobLevel::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (JobTitle $jobTitle): void {
            // Update users that have this job title to null
            // Note: job_title_id column no longer exists in users table
            // This is kept for backward compatibility if any references still exist
            if (Schema::hasColumn('users', 'job_title_id')) {
                User::query()
                    ->where('job_title_id', $jobTitle->id)
                    ->update(['job_title_id' => null]);
            }
        });
    }
}
