<?php

namespace App\Policies;

use App\Models\User;
use App\Models\order;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    public function modify(User $user, order $order): Response
    {
        return $user->id === $order->user_id
        ? Response::allow()
        : Response::deny('You do not own this order.');
    }
}
