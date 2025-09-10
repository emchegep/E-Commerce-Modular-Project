<?php

namespace Modules\Order\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Payment\Payment;
use Modules\Product\CartItem;
use Modules\Product\CartItemCollection;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_in_cents',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'total_in_cents' => 'integer',
    ];

    public const PENDING = 'pending';

    public const COMPLETED = 'completed';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function lastPayment(): HasOne
    {
        return $this->payments()->one()->latest();
    }

    public function url(): string
    {
        return route('order:orders.show', $this);
    }

    public static function startForUser(int $userId): self
    {
        return self::make([
            'user_id' => $userId,
            'status' => self::PENDING,
        ]);
    }

    /**
     * @param  CartItemCollection<CartItem>  $items
     */
    public function addLinesFromCartItems(CartItemCollection $items): void
    {
        foreach ($items->items() as $item) {
            $this->lines->push(OrderLine::make([
                'product_id' => $item->product->id,
                'quantity' => $item->quantity,
                'product_price_in_cents' => $item->product->priceInCents,
            ]));
        }

        $this->total_in_cents = $this->lines->sum(fn (OrderLine $line) => $line->product_price_in_cents);
    }

    public function fulfill(): void
    {
        $this->status = self::COMPLETED;

        $this->save();
        $this->lines()->saveMany($this->lines);
    }
}
