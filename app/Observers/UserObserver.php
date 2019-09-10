<?php

namespace App\Observers;

use App\Models\User;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class UserObserver
{
    public function creating(User $user)
    {
        //
    }

    public function updating(User $user)
    {
        //
    }

    public function saving(User $user)
    {
        // Only set default avatar if user avatar is empty
        if(empty($user->avatar)){
            $user->avatar = 'https://cdn.learnku.com/uploads/images/201710/30/1/TrJS40Ey5k.png';
        }
    }
}
