<?php

namespace App\Controllers;

use App\Service\Response;

class Root implements ControllerInterface
{
    /**
     *
     * @inheritDoc
     */
    public function process(): Response
    {
        $response = new Response(200);
        $response->setContent([
            'code' => 'OK',
            'message' => '...',
        ]);
        return $response;
    }
}
