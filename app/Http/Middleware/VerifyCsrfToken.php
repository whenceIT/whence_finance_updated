<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'admin.billing.payments.stripe_webhook',
        'billing/payment/stripe/webhook',
        'billing/payment/paynow/webhook',
        'file/upload',
    ];
}
