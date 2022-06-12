<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'phone', 'location_id'];
    
    public function location() {
        return $this->hasOne(Location::class, 'id', 'location_id');
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
        if (isset($filters['name']) && !empty($filters['name'])) {
            $query->where('name', 'like', '%' .$filters['name'] . '%');
        }
    }

}
