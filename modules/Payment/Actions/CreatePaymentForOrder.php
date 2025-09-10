<?php

namespace Modules\Payment\Actions;

use Modules\Order\Exceptions\PaymentFailedException;
use Modules\Payment\PayBuddy;
use Modules\Payment\Payment;
use RuntimeException;

final class CreatePaymentForOrder
{
    /**
     * @throws PaymentFailedException
     */
    public function handle(
        int $orderId,
        int $userId,
        int $totalInCents,
        PayBuddy $payBuddy,
        string $paymentToken,
    ): void {
        try {
            $charge = $payBuddy->charge(
                $paymentToken,
                $totalInCents,
                'Modularization'
            );
        } catch (RuntimeException) {
            throw PaymentFailedException::dueToInvalidToken();
        }

        $payment = Payment::query()->create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'total_in_cents' => $totalInCents,
            'status' => 'paid',
            'payment_gateway' => 'PayBuddy',
            'payment_id' => $charge['id'],
        ]);
    }
}
