<?php namespace Znck\Trust\Http\Middleware;

abstract class AbstractRoleOrPermission
{
    protected $request;

    protected function getAction($key)
    {
        $action = $this->request->route()->getAction();
        dd($this->request->route());

        return isset($action[$key]) ? $action[$key] : false;
    }

    protected function hasRole()
    {
        $role = $this->getAction('role');

        return $this->request->user() and $role and $this->request->user()->hasRole($role);
    }

    protected function hasPermission()
    {
        $do = $this->getAction('permission');

        return $this->request->user() and $do and $this->request->user()->can($do);
    }

    protected function abort()
    {
        if ($this->request->isJson() || $this->request->wantsJson()) {
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

}
