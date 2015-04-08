<?php namespace Znck\Trust\Http\Middleware;

use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Session\Store;

/**
 * Class AbstractRoleOrPermission
 *
 * @package Znck\Trust\Http\Middleware
 */
abstract class AbstractRoleOrPermission
{

    /**
     * @type \Illuminate\Routing\Route
     */
    protected $route;
    /**
     * @type \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @type \Znck\Trust\Contracts\HasRole|null
     */
    protected $user;

    /**
     * @type \Illuminate\Session\Store
     */
    private $session;

    /**
     * @param \Illuminate\Routing\Route $route
     * @param \Illuminate\Auth\Guard    $auth
     * @param \Illuminate\Session\Store $session
     * @param \Illuminate\Http\Request  $request
     */
    function __construct(Route $route, Guard $auth, Store $session, Request $request)
    {
        $this->route = $route;
        $this->user = $auth->user();
        $this->session = $session;
        $this->request = $request;
    }

    /**
     * @param $key
     *
     * @return string
     */
    protected function getAction($key)
    {
        $action = array_get($this->route->getAction(), $key, $this->getActionFallback($key));
        if (null === $action) {
            /**
             * Defining middleware `role` and `permission` in constructor is not supported.
             */
            abort(500);
        }

        return $action;
    }

    /**
     * @return bool
     */
    protected function hasRole()
    {
        $role = $this->getAction('role');

        return $this->user->ability($role, [], ['validate_all' => true]);
    }

    /**
     * Check if user has the requested permissions.
     *
     * @return bool
     */
    protected function hasPermission()
    {
        $permissionsNeeded = $this->getAction('permission');

        return $this->user->ability([], $permissionsNeeded, ['validate_all' => true]);
    }

    /**
     * Abort request with Unauthorized response code.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    protected function abort(Request $request)
    {
        if ($request->isJson() || $request->wantsJson()) {
            return response()->json([
                'error' => [
                    'code'        => 401,
                    'status'      => 'Unauthorized',
                    'description' => 'You are not authorized to access this resource.',
                ]
            ], 401);
        }

        abort(401, 'Your are not authorized to access this resource.');

        return null;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    protected function getActionFallback($key)
    {
        return $this->session->get('znck.trust.' . $key, null);
    }

}