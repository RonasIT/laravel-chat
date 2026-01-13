<?php

namespace RonasIT\Chat\Contracts\Requests;

interface RequestContract
{
    /**
     * Get the validated data from the request.
     *
     * @param  array|string  $keys
     * @param  mixed  $default
     *
     * @return array;
     */
    public function onlyValidated($keys = null, $default = null);

    public function rules(): array;

    public function authorize();

    public function validateResolved();

    public function input();
}
