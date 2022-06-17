<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'delegate_id',
        'quantity',
        'status',
        'inventory_quantity',
        'is_from_warehouse',
        'return_invoice_id',
        'reviewier_id'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {

        // When a user created a purchase, we need to check purchase quantity then compare it to total quantity
        // total quantity is the rest of total quantity minus the purchase quantity
        // if rest quantity is less than total quantity we update order status to quantity_mismatch

        static::created(function ($purchase) {

            $order = $purchase->order;

            $total_quantity = $order->products->sum('quantity');
            $purchased_quantity = $order->purchases->sum('quantity');

            $rest_quantity = $total_quantity - $purchased_quantity;

            if ($rest_quantity > 0) {
                $order->status = 'uncompleted_quantity';
            } else {
                $order->status = 'purchased';
            }

            $order->save();
        });
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function delegate()
    {
        return $this->hasOne(User::class, 'id', 'delegate_id');
    }

    public function reviewer()
    {
        return $this->hasOne(User::class, 'id', 'reviewier_id');
    }

    /**
     * Scope a query to filter products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeFilter($query, $filters)
    {   
        // if status not passed we return only completed and under_review purchases
        if (isset($filters['status'])) {
            $query->where('status', '=', $filters['status']);
        }else{
            $query->whereIn('status', ['completed', 'under_review']);
        }
    }
}
