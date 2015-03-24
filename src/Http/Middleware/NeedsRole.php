<?php namespace Znck\Trust\Http\Middleware;

use Closure;

class NeedsRoleOr extends AbstractRoleOrPermission
{

    /**
     * Handle an incoming request.
     *
     * @param  $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->request = $request;

        if ($this->hasRole()) {
            return $next($request);
        }

        return $this->abort($request);
    }

}
