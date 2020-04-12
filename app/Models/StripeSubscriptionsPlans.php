<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeSubscriptionsPlans extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stripe_subscription_plans';
}
