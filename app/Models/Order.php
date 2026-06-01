<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'type',
        'subtotal',
        'delivery_fee',
        'total',
        'notes',

        // ✅ ADD THESE
        'payment_method',
        'payment_reference',
        'payment_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function statusList(): array
    {
        return ['pending','confirmed','preparing','ready','completed','cancelled'];
    }
}