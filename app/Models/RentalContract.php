<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'tenant_id',
        'start_date',
        'end_date',
        'monthly_rent',
        'deposit',
        'status',
        'owner_approval_required',
        'owner_approval_status',
        'owner_approval_at',
        'approved_by_owner_id',
        'owner_approval_notes',
        'notes',
        'contract_file',
        'electricity_account_number',
        'water_account_number',
    ];

    protected $casts = [
        'start_date'              => 'date',
        'end_date'                => 'date',
        'owner_approval_required' => 'boolean',
        'owner_approval_at'       => 'datetime',
    ];

    public function approvingOwner()
    {
        return $this->belongsTo(Owner::class, 'approved_by_owner_id');
    }

    public function needsOwnerApproval(): bool
    {
        return $this->owner_approval_required
            && $this->owner_approval_status !== 'approved';
    }

    public function ownerApprovalStatusLabel(): string
    {
        if (app()->getLocale() === 'en') {
            return match ($this->owner_approval_status) {
                'pending'  => 'Awaiting owner approval',
                'approved' => 'Approved by owner',
                'rejected' => 'Rejected by owner',
                default    => $this->owner_approval_required ? 'Awaiting owner approval' : '—',
            };
        }

        return match ($this->owner_approval_status) {
            'pending'  => 'بانتظار موافقة المالك',
            'approved' => 'تمت موافقة المالك',
            'rejected' => 'مرفوض من المالك',
            default    => $this->owner_approval_required ? 'بانتظار موافقة المالك' : '—',
        };
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paidPayments()
    {
        return $this->hasMany(Payment::class)->where('status', 'paid');
    }

    public function pendingPayments()
    {
        return $this->hasMany(Payment::class)->where('status', 'pending');
    }
}
