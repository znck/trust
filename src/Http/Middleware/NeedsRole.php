<?php namespace Znck\Trust\Http\Middleware;

use Closure;

class NeedsRole extends AbstractRoleOrPermission
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

        if (!$this->user) {
            return redirect()->guest(config('app.login', url('/auth/login')));
        }

        if ($this->hasRole()) {
            return $next($request);
        }

        return $this->abort($request);
    }

}