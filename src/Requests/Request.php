<?php

namespace Chriha\LaravelApi\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class Request extends FormRequest
{

    /**
     * Requested resource
     * @var mixed
     */
    protected $resource;


    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validate()
    {
        parent::validate();

        if ( method_exists( $this, 'handleRequest' ) )
        {
            $this->handleRequest();
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Return the requested resource
     *
     * @return mixed|null
     */
    public function getResource()
    {
        return $this->resource;
    }

    protected function throwResponseException( $errors )
    {
        if ( $this->expectsJson() )
        {
            $errors = [
                'errors' => $errors
            ];
        }

        throw new HttpResponseException( $this->response( $errors ) );
    }

}