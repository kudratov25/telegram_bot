<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'menu_id',
        'description',
        'image_url',
        'price',
        'is_available'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
