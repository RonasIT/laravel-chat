<?php

namespace RonasIT\Chat\Contracts\Requests;

interface RequestContract
{
    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    function validated();

    function rules(): array;

    function authorize();

    function validateResolved();

    function input();
}
