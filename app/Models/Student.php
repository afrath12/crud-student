<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Student extends Model
{
    use HasFactory, LogsActivity; 

    // Define your mass assignable fields
    protected $fillable = [
        'name',
        'image',
        'email', 
    ];

    // Configure exactly what the package should track
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'image', 'email']) // Columns to track for changes
            ->logOnlyDirty() // Only log if the data actually changed
            ->dontSubmitEmptyLogs();
    }
}