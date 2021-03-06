<?php

namespace App\JsonApi\Articles;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use CloudCreativity\LaravelJsonApi\Auth\AbstractAuthorizer;

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
     *      the domain record type.
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
     *      the domain record type.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function create($type, $request)
    {
        $this->authenticate();

        if ($request->has('data.relationships.authors')) {
            $this->authorize('create', $type);
        }
    }

    /**
     * Authorize a resource read request.
     *
     * @param object $article
     *      the domain record.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function read($article, $request)
    {
        // TODO: Implement read() method.
    }

    /**
     * Authorize a resource update request.
     *
     * @param object $article
     *      the domain record.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function update($article, $request)
    {
        $this->can('update', $article);
    }

    /**
     * Authorize a resource read request.
     *
     * @param object $article
     *      the domain record.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function delete($article, $request)
    {
        $this->can('delete', $article);
    }

    /**
    * Authorize a modify relationship request.
    *
    * @param object $record
    *      the domain record.
    * @param string $field
    *      the JSON API field name for the relationship.
    * @param Request $request
    *      the inbound request.
    * @return void
    * @throws AuthenticationException|AuthorizationException
    *      if the request is not authorized.
    */
    public function modifyRelationship($record, $field, $request)
    {
        $ability = Str::camel('modify-'.$field);

        $this->can($ability, $record);
    }

}
