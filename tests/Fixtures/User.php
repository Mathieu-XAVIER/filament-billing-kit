<?php

namespace Mxavier\FilamentBillingKit\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use Mxavier\FilamentBillingKit\Traits\HasEntitlements;

class User extends Authenticatable
{
    use Billable;
    use HasEntitlements;

    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password', 'stripe_id'];
}
