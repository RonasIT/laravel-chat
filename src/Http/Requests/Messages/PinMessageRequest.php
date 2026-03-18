<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\PinMessageRequestContract;

class PinMessageRequest extends BaseMessageRequest implements PinMessageRequestContract
{
    public function validateResolved(): void
    {
        $this->init();

        $this->checkMessageExists();

        parent::validateResolved();
    }
}
