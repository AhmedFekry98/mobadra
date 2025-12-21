<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\SystemManagements\Models\User;
use App\Features\SystemManagements\Requests\AssignRoleRequest;
use App\Traits\ApiResponses;
use Illuminate\Routing\Controller;


class UserController extends Controller
{
    use ApiResponses;

    private static $model = User::class;


    /**
     * assign roles to user
     */


}
