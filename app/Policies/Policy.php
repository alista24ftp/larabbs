<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function before($user, $ability)
	{
	    // if ($user->isSuperAdmin()) {
	    // 		return true;
        // }

        // If user has permission to manage contents, then user is authorized
        if($user->can('manage_contents')){
            return true;
        }
	}
}
