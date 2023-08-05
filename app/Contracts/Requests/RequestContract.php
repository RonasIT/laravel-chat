<?php

namespace App\Contracts\Requests;

interface RequestContract
{
    /**
     * Get the validated data from the request.
     *
     * @param array|string $keys
     * @param mixed $default
     *
     * @return array;
     */
    function onlyValidated($keys = null, $default = null);

    function rules(): array;

    function authorize();

    function validateResolved();

    function input();
}
