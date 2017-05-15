<?php

namespace Chriha\LaravelApi\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{

    /** @var Request */
    protected $request;

    /** @var int */
    protected $statusCode = Response::HTTP_OK;

    /**
     * Contains the item per page limit
     * @var int
     */
    protected $limit = 100;


    /**
     * Controller constructor
     */
    public function __construct()
    {
        $this->request = \Request::instance();

        if ( $this->request->has( 'limit' ) )
        {
            $this->limit = (int)$this->request->get( 'limit' );
        }
    }

    /**
     * Return the data with its headers
     *
     * @param array $data Data to respond
     * @param array $headers Headers to use for the response
     * @return Response
     */
    public function respond( array $data, array $headers = [] ) : Response
    {
        return response()->json( $data, $this->getStatusCode(), $headers );
    }

    /**
     * Normal response
     *
     * @param array $data Message to show
     * @return Response
     */
    public function respondData( array $data ) : Response
    {
        return $this->setStatusCode( Response::HTTP_OK )->respond( [ 'data' => $data ] );
    }

    /**
     * Responding with pagination
     *
     * @param array $data Message to show
     * @param LengthAwarePaginator $paginator
     * @return Response
     */
    public function respondWithPagination( array $data, LengthAwarePaginator $paginator ) : Response
    {
        if ( $paginator )
        {
            $paginator->appends( $this->request->all() );
        }

        return $this->setStatusCode( Response::HTTP_OK )
            ->respond( [
                'meta' => [
                    'total'    => $paginator->total(),
                    'pages'    => ceil( $paginator->total() / $paginator->perPage() ),
                    'current'  => $paginator->currentPage(),
                    'limit'    => $paginator->perPage(),
                    'next'     => $paginator->nextPageUrl(),
                    'previous' => $paginator->previousPageUrl(),
                ],
                'data' => $data,
            ] );
    }

    /**
     * Responding with pagination
     *
     * @param array $data Message to show
     * @param array $meta The paginator object
     * @return Response
     */
    public function respondWithMeta( array $data, array $meta = [] ) : Response
    {
        return $this->respond( [
            'meta' => $meta,
            'data' => $data,
        ] );
    }

    /**
     * Responding an error
     *
     * @param string|array $message Message to show
     * @return Response
     */
    public function respondWithError( $message ) : Response
    {
        $messages = ! is_string( $message ) ? $message : [ $message ];

        return $this->setStatusCode( Response::HTTP_BAD_REQUEST )->respond( [ 'errors' => $messages ] );
    }

    /**
     * Responding a message
     *
     * @param string $message Message to show
     * @param array $data
     * @return Response
     */
    public function respondWithMessage( string $message, array $data = null ) : Response
    {
        if ( ! is_null( $data ) )
        {
            return $this->respond( [
                'message' => $message,
                'data'    => $data,
            ] );
        }

        return $this->respond( [ 'message' => $message ] );
    }

    /**
     * Responding a not found error
     *
     * @param string $message Message to show for this error
     * @return Response
     */
    public function respondNotFound( string $message = 'Resource not found.' ) : Response
    {
        return $this->setStatusCode( Response::HTTP_NOT_FOUND )->respond( [ 'errors' => [ $message ] ] );
    }

    /**
     * Responding a forbidden error
     *
     * @param string $message Message to show for this error
     * @return Response
     */
    public function respondNoSubscription( string $message = 'This account has no valid subscription.' ) : Response
    {
        return $this->setStatusCode( Response::HTTP_FORBIDDEN )->respond( [ 'errors' => [ $message ] ] );
    }

    /**
     * Responding an internal error
     *
     * @param string $message Message to show for this error
     * @return Response
     */
    public function respondInternalError( string $message = 'Internal error.' ) : Response
    {
        return $this->setStatusCode( Response::HTTP_INTERNAL_SERVER_ERROR )->respond( [ 'errors' => [ $message ] ] );
    }

    /**
     * Returning an unprocessable entity error
     *
     * @param string $message Message to return
     * @return Response
     */
    public function respondUnprocessableEntity( string $message = 'Unprocessable entity.' ) : Response
    {
        return $this->setStatusCode( Response::HTTP_UNPROCESSABLE_ENTITY )->respond( [ 'errors' => [ $message ] ] );
    }

    /**
     * Returning an unauthorized error
     *
     * @param string $message Message to return
     * @return Response
     */
    public function respondUnauthorized( string $message = 'Unauthorized access.' ) : Response
    {
        return $this->setStatusCode( Response::HTTP_UNAUTHORIZED )->respond( [ 'errors' => [ $message ] ] );
    }

    /**
     * Returning a created respond
     *
     * @param array $data
     * @return Response
     */
    public function respondCreated( array $data = null ) : Response
    {
        return $this->setStatusCode( Response::HTTP_CREATED )->respondWithMessage( 'Resource created successfully.', $data );
    }

    /**
     * Returning a updated respond
     *
     * @param  array $data
     * @return Response
     */
    public function respondUpdated( array $data = null ) : Response
    {
        return $this->setStatusCode( Response::HTTP_OK )->respondWithMessage( 'Resource updated successfully.', $data );
    }

    /**
     * Returning a deleted respond
     *
     * @return Response
     */
    public function respondDeleted() : Response
    {
        return $this->setStatusCode( Response::HTTP_OK )->respondWithMessage( 'Resource deleted successfully.' );
    }

    /**
     * Getter for $statusCode
     *
     * @return int
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    /**
     * Setter for $statusCode
     *
     * @param int $statusCode HTTP-Status code to set
     * @return self
     */
    public function setStatusCode( int $statusCode ) : self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

}