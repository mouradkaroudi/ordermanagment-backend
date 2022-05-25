<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestedProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image_id',
        'store_id',
        'category_id',
        'delivery_method_id',
        'user_id',
        'sell_price',
        'sku',
        'cost',
        'is_new',
        'status'
    ];

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function store() {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }

    public function image()
    {
        return $this->hasOne(File::class, 'id', 'image_id');
    }

    public function scopeFilter($query, $filters)
    {   
        // by default we retieve the added products
        if(isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }else{
            $query->where('status', 'added');
        }
    }

}
