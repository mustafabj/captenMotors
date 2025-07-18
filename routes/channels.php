<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel for user notifications
Broadcast::channel('user.{userId}.notifications', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Channel for equipment cost notifications
Broadcast::channel('user.{userId}.equipment-cost-notifications', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
}); 