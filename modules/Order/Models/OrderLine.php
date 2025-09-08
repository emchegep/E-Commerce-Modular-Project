<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderLine query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderLine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderLine extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'product_price_in_cents',
    ];
}
