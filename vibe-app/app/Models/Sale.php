<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['sale_number', 'customer_id', 'user_id', 'subtotal', 'vat', 'total', 'payment_method', 'amount_paid', 'change_amount', 'status'];

    protected $casts = ['subtotal' => 'decimal:2', 'vat' => 'decimal:2', 'total' => 'decimal:2', 'amount_paid' => 'decimal:2', 'change_amount' => 'decimal:2'];

    public function items() { return $this->hasMany(SaleItem::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
}
