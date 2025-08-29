<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;  // add this

class Expense extends Model
{
    use Notifiable;  // add this trait

    protected $fillable = [
        'date',
        'warehouse_id',
        'expense_category_id',
        'amount',
        'reference_code',
        'title',
        'details',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
