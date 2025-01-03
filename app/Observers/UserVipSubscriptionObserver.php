<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserVipSubscription;

class UserVipSubscriptionObserver
{
    public function updated(UserVipSubscription $subscription)
    {
        if (
            $subscription->isDirty('payment_status') &&
            $subscription->payment_status === 'completed'
        ) {

            $user = User::find($subscription->user_id);
            $user->update(['is_vip' => true]);
        }
    }
}