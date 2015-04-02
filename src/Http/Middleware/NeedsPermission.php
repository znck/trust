<?php namespace Znck\Trust\Http\Middleware;

use Closure;

class NeedsPermission extends AbstractRoleOrPermission
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
            return redirect()->guest(\Config::get('app.login', url('/auth/login')));
        }

        if ($this->hasPermission()) {
            return $next($request);
        }

        return $this->abort($request);
    }
}
