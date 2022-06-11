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
        'supplier_id',
        'location_id',
        'category_id',
        'cost',
        'is_paid',
    ];

    public function supplier()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
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
            $query->where('ref', '=', $filters['product_ref_sku'])->orWhere('sku', '=', $filters['product_ref_sku']);
        }

        if (isset($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (isset($filters['location'])) {
           $query->where('location_id', $filters['location']);
        }

        if (isset($filters['supplier'])) {
            $query->where('supplier_id', $filters['supplier']);
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
