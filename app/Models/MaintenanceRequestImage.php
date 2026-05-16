<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MaintenanceRequestImage extends Model
{
    protected $fillable = ['maintenance_request_id', 'path'];

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function url(): string
    {
        return $this->path ? Storage::disk('public')->url($this->path) : '';
    }
}
