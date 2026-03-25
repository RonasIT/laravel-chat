<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\ReadMessagesRequestContract;

class ReadMessagesRequest extends BaseMessageRequest implements ReadMessagesRequestContract
{
    public function validateResolved(): void
    {
        $this->init();

        $this->checkMessageExists();

        parent::validateResolved();
    }
}
