<?php

namespace Chriha\LaravelApi\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

trait Api
{

    /** @var \Request */
    protected $request;

    /** @var int */
    protected $statusCode = Response::HTTP_OK;

    /** @var int */
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
    public function respond( array $data, array $headers = [] )
    {
        return response()->json( $data, $this->getStatusCode(), $headers );
    }

    /**
     * Normal response
     *
     * @param array $data Message to show
     * @return Response
     */
    public function respondData( array $data )
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
    public function respondWithPagination( array $data, LengthAwarePaginator $paginator )
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
    public function respondWithMeta( array $data, array $meta = [] )
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
    public function respondWithError( $message )
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
    public function respondWithMessage( $message, array $data = null )
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
    public function respondNotFound( $message = 'Resource not found.' )
    {
        return $this->setStatusCode( Response::HTTP_NOT_FOUND )->respond( [ 'errors' => [ $message ] ] );
    }

    /**
     * Responding a forbidden error
     *
     * @param string $message Message to show for this error
     * @return Response
     */
    public function respondNoSubscription( $message = 'This account has no valid subscription.' )
    {
        return $this->setStatusCode( Response::HTTP_FORBIDDEN )->respond( [ 'errors' => [ $message ] ] );
    }

    /**
     * Responding an internal error
     *
     * @param string $message Message to show for this error
     * @return Response
     */
    public function respondInternalError( $message = 'Internal error.' )
    {
        return $this->setStatusCode( Response::HTTP_INTERNAL_SERVER_ERROR )->respond( [ 'errors' => [ $message ] ] );
    }

    /**
     * Returning an unprocessable entity error
     *
     * @param string $message Message to return
     * @return Response
     */
    public function respondUnprocessableEntity( $message = 'Unprocessable entity.' )
    {
        return $this->setStatusCode( Response::HTTP_UNPROCESSABLE_ENTITY )->respond( [ 'errors' => [ $message ] ] );
    }

    /**
     * Returning an unauthorized error
     *
     * @param string $message Message to return
     * @return Response
     */
    public function respondUnauthorized( $message = 'Unauthorized access.' )
    {
        return $this->setStatusCode( Response::HTTP_UNAUTHORIZED )->respond( [ 'errors' => [ $message ] ] );
    }

    /**
     * Returning a created respond
     *
     * @param array $data
     * @return Response
     */
    public function respondCreated( array $data = null )
    {
        return $this->setStatusCode( Response::HTTP_CREATED )->respondWithMessage( 'Resource created successfully.', $data );
    }

    /**
     * Returning a updated respond
     *
     * @param  array $data
     * @return Response
     */
    public function respondUpdated( array $data = null )
    {
        return $this->setStatusCode( Response::HTTP_OK )->respondWithMessage( 'Resource updated successfully.', $data );
    }

    /**
     * Returning a deleted respond
     *
     * @return Response
     */
    public function respondDeleted()
    {
        return $this->setStatusCode( Response::HTTP_OK )->respondWithMessage( 'Resource deleted successfully.' );
    }

    /**
     * Getter for $statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for $statusCode
     *
     * @param int $statusCode HTTP-Status code to set
     * @return self
     */
    public function setStatusCode( $statusCode )
    {
        $this->statusCode = $statusCode;

        return $this;
    }

}