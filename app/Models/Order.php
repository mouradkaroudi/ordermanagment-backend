<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'product_cost',
        'is_paid',
        'total_amount',
        'quantity', 
        'status'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleted(function ($order) {
            $order->purchase_order()->delete();
        });
    }


    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->withTrashed();
    }

    public function purchase_order()
    {
        return $this->hasOne(PurchaseOrder::class, 'order_id', 'id');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['status'])) {
            if ($filters['status'] == 'not_sent') {
                $query->whereRaw('status is null');
            } else {
                $query->where('status', '=', $filters['status']);
            }
        }

        if (!isset($filters['show']) || $filters['show'] === 'today') {
            $query->whereDate('created_at', Carbon::today());
        }

        if (isset($filter['location'])) {
        }

        if (isset($filter['delegate'])) {
            $query->whereRelation('purchase_order', 'delegate_id',0);
        }
    }
}
