<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */

    function getChildrenIds($user)
    {
        $ids = [$user->id];

        foreach ($user->children as $child) {
            $ids = array_merge($ids, getChildrenIds($child));
        }

        return $ids;
    }
}
