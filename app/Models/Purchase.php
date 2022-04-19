<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['total_cost'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleted(function ($purchase) {
            $purchase->orders()->delete();
        });
    }

    public function orders()
    {
        return $this->hasMany(PurchaseOrder::class, 'purchase_id', 'id');
    }

}
