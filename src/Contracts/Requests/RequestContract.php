<?php

namespace RonasIT\Chat\Contracts\Requests;

interface RequestContract
{
    public function onlyValidated(array $keys = []): array;

    public function rules(): array;

    public function authorize();

    public function validateResolved();

    public function input();
}
