<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['purchase_id', 'order_id', 'delegate_id'];

    public function delegate() {
        return $this->hasOne(User::class, 'id', 'delegate_id');
    }

    public function order() {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

}
