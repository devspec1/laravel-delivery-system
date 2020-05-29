<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriversSubscriptions extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stripe_subscriptions';

    protected $fillable = ['stripe_id', 'status', 'email', 'plan', 'country','card_name',];
}
