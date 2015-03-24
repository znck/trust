<?php namespace Znck\Trust\Http\Middleware;

use Closure;

class NeedsOrPermission extends AbstractRoleOrPermission
{


    /**
     * Handle an incoming request.
     *
     * @param           $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->request = $request;

        if ($this->hasPermission()) {
            return $next($request);
        }

        return $this->abort($request);
    }
}
