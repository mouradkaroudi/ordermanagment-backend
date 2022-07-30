<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    public $table = 'order_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'store_id',
        'user_id',
        'total_amount',
        'quantity'
    ];

    protected static function booted() {
        /*
        static::deleted(function ($orderProduct) {
            
            $order_id = $orderProduct->order_id;

            if(OrderProduct::where('order_id', $order_id)->count() == 0) {
                Order::find($order_id)->delete();
            }

        });*/
    }

    public function order() {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id')->withTrashed();
    }

    public function store() {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }

}
