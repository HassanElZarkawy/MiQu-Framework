<?php

namespace Miqu\Core;

trait BufferAwareTrait
{
    /**
     * @return bool
     */
    public function outputBufferStarted(): bool
    {
        return ob_get_length() > 0;
    }

    /**
     * @return void
     */
    public function clearOutputBuffer() : void
    {
        ob_clean();
    }
}