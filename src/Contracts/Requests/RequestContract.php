<?php

namespace RonasIT\Chat\Contracts\Requests;

interface RequestContract
{
    /**
     * Get the validated data from the request.
     *
     * @param  array  $keys
     *
     * @return array;
     */
    public function onlyValidated(array $keys = []): array;

    public function rules(): array;

    public function authorize();

    public function validateResolved();

    public function input();
}
