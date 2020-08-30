<?php

namespace App\Controllers;

use App\Service\Response;

interface ControllerInterface
{
    /**
     * @return Response
     */
    public function process(): Response;
}
