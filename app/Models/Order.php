<?php

namespace App\Models;

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
    protected $fillable = ['product_id', 'quantity', 'status'];

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function purchase_order() {
        return $this->hasOne(PurchaseOrder::class, 'order_id', 'id');
    }

}
