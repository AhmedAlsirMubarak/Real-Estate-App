<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseInvoice extends Model
{
    protected $fillable = ['expense_id', 'file_path', 'original_name'];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
