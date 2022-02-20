<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

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

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['ref'])) {
            $query->where('ref', '=', $filters['ref']);
        }
    }
}
