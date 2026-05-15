<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'unit_id',
        'title',
        'description',
        'priority',
        'status',
        'employee_notes',
        'required_tools',
        'requires_external_worker',
        'external_worker_name',
        'external_worker_cost',
        'assigned_employee_id',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'requires_external_worker' => 'boolean',
        'external_worker_cost' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(User::class, 'assigned_employee_id');
    }

    public function priorityLabel(): string
    {
        return match($this->priority) {
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
            default => $this->priority,
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'rejected' => 'مرفوض',
            default => $this->status,
        };
    }
}
