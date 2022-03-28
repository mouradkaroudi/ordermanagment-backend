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
    protected $fillable = ['product_id', 'quantity', 'status'];

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
            if($filters['status'] == 'not_sent') {
                $query->whereRaw('status is null');
            }else{
                $query->where('status', '=', $filters['status']);
            }
        }

        if(!isset($filters['show']) || $filters['show'] === 'today') {
            $query->whereDate('created_at', Carbon::today());
        }

        if(isset($filter['location'])) {
            
        }

        if(isset($filter['supplier'])) {

        }

    }
}
