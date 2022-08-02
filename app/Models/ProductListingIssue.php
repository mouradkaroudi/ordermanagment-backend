<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductListingIssue extends Model
{
    use HasFactory;

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    // const UPDATED_AT = 'resolved_at';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'issue',
        'created_by',
        'created_at',
        'resolved_by',
        'resolved_at'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($issue) {
            Product::where('id', $issue->product_id)->update([
                'is_available' => false,
            ]);
        });
    }

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id')->withTrashed();
    }

    public function resolved_user() {
        return $this->hasOne(User::class, 'id', 'resolved_by');
    }

    public function created_user() {
        return $this->hasOne(User::class, 'id', 'created_by');
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

            $query->whereHas('product', function ($query) use ($filters) {
                $query->where('ref', 'like', '%'.$filters['product_ref_sku'].'%')->orWhere('sku', 'like', '%'.$filters['product_ref_sku'].'%');
            });

        }

        if (isset($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }
    }
}
