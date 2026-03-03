<?php

namespace RonasIT\Chat\Repositories;

use Illuminate\Support\Arr;
use RonasIT\Support\Repositories\BaseRepository as RonasITRepository;

class BaseRepository extends RonasITRepository
{
    public function insertOrIgnore(array $data): bool
    {
        $defaultTimestamps = [];

        if ($this->model->timestamps) {
            $now = now();

            $defaultTimestamps = [
                $this->model::CREATED_AT => $now,
                $this->model::UPDATED_AT => $now,
            ];
        }

        $data = array_map(function ($item) use ($defaultTimestamps) {
            $fillableFields = Arr::only($item, $this->model->getFillable());

            return array_merge($defaultTimestamps, $fillableFields);
        }, $data);

        $this->postQueryHook();

        return $this->model->insertOrIgnore($data);
    }
}
