<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        'delegate_id',
        'status'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::updated(function ($order) {
            if($order->status === 'unavailable_quantity') {

                $user = Auth::user();

                ProductListingIssue::create([
                    'product_id' => $order->product_id,
                    'created_by' => $user->id,
                    'created_at' => Carbon::now()
                ]);
            }
        });
        /*
        static::deleted(function ($order) {
            $order->purchase_order()->delete();
        });*/
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->withTrashed();
    }

    public function delegate()
    {
        return $this->hasOne(User::class, 'id', 'delegate_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'order_id', 'id');
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

        if(!isset($filters['show']) && isset($filters['date']) && !empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }

        if (isset($filters['supplier'])) {
            
            $suppliers = Supplier::where('name', 'like', '%'. $filters['supplier'].'%')->pluck('id')->toArray();

            $query->whereHas('product', function ($query) use ($suppliers) {
                $query->whereHas('suppliers', function($query) use ($suppliers) {
                    $query->whereIn('supplier_id', $suppliers);
                });
            });
        }

        if(isset($filters['product_ref_sku'])) {
            $query->whereHas('product', function ($query) use ($filters) {
                $query->where('ref', 'like', '%'.$filters['product_ref_sku'].'%')->orWhere('sku', 'like', '%'.$filters['product_ref_sku'].'%');
            });
        }

        if (isset($filters['supplier_id']) && !empty($filters['supplier_id']) && $filters['supplier_id'] != 0) {

            $supplier_id = $filters['supplier_id'];

            $query->whereHas('product', function ($query) use ($supplier_id) {
                $query->whereHas('suppliers', function($query) use ($supplier_id) {
                    $query->where('supplier_id', '=', $supplier_id);
                });
            });
        }

        if (isset($filters['location'])) {
        
            $suppliers = Supplier::where('location_id', '=',$filters['location'])->pluck('id')->toArray();

            $query->whereHas('product', function ($query) use ($suppliers) {
                $query->whereHas('suppliers', function ($query) use ($suppliers) {
                    $query->whereIn('supplier_id', $suppliers);
                });
            });
        }

        if (isset($filters['delegate'])) {
            $query->where('delegate_id', $filters['delegate']);
        }
    }
}
