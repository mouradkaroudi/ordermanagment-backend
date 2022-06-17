<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ref',
        'name',
        'image_id',
        'sku',
        'mainRef',
        'category_id',
        'cost',
        'is_paid',
    ];

    public function suppliers()
    {
        return $this->hasMany(ProductSuppliers::class, 'product_id', 'id');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function image()
    {
        return $this->hasOne(File::class, 'id', 'image_id');
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
        if (isset($filters['product_ref_sku']) && !empty($filters['product_ref_sku'])) {
            $query->where('ref', 'like', '%'.$filters['product_ref_sku'].'%')->orWhere('sku', 'like', '%'.$filters['product_ref_sku'].'%');
        }

        if (isset($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (isset($filters['supplier'])) {
            
            $suppliers = Supplier::where('name', 'like', '%'. $filters['supplier'].'%')->pluck('id')->toArray();
            
            $query->whereHas('suppliers', function ($query) use ($suppliers) {
                $query->whereIn('supplier_id', $suppliers);
            });
        }


        if (isset($filters['no_cost']) && !empty($filters['no_cost'])) {
            $no_cost = filter_var($filters['no_cost'], FILTER_VALIDATE_BOOLEAN);
            if ($no_cost) {
                $query->where('cost', '=', 0);
            }
        }

        if (isset($filters['no_main_ref']) && !empty($filters['no_main_ref'])) {
            $no_main_ref = filter_var($filters['no_main_ref'], FILTER_VALIDATE_BOOLEAN);
            if ($no_main_ref) {
                $query->where('mainRef', '=', null);
            }
        }

        if (isset($filters['is_paid']) && !empty($filters['is_paid'])) {
            $is_paid = filter_var($filters['is_paid'], FILTER_VALIDATE_BOOLEAN);
            if ($is_paid) {
                $query->where('is_paid', '=', 1);
            }
        }


    }
}
