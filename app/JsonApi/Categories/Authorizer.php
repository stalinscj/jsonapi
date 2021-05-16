<?php

namespace App\JsonApi\Categories;

use CloudCreativity\LaravelJsonApi\Auth\AbstractAuthorizer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class Authorizer extends AbstractAuthorizer
{
    /**
     * The guards to use to authenticate a user.
     *
     * @var array
     */
    protected $guards = ['sanctum'];
    
    /**
     * Authorize a resource index request.
     *
     * @param string $type
     *      the domain category type.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function index($type, $request)
    {
        // TODO: Implement index() method.
    }

    /**
     * Authorize a resource create request.
     *
     * @param string $type
     *      the domain category type.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function create($type, $request)
    {
        $this->authenticate();
    }

    /**
     * Authorize a resource read request.
     *
     * @param object $category
     *      the domain category.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function read($category, $request)
    {
        // TODO: Implement read() method.
    }

    /**
     * Authorize a resource update request.
     *
     * @param object $category
     *      the domain category.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function update($category, $request)
    {
        $this->authenticate();
    }

    /**
     * Authorize a resource read request.
     *
     * @param object $category
     *      the domain category.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function delete($category, $request)
    {
        $this->authenticate();
    }

}
