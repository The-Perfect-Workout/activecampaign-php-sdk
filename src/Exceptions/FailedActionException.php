<?php

namespace PerfectWorkout\ActiveCampaign\Exceptions;

use Exception;

class FailedActionException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param $message
     * @param $code
     */
    public function __construct($message, $code = 400)
    {
        parent::__construct($message, $code);
    }
}
