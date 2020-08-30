<?php

namespace App\Controllers;

use App\Service\Response;

class NotFound implements ControllerInterface
{
    /**
     * @inheritDoc
     */
    public function process(): Response
    {
        $response = new Response(404);
        $response->setError([
            'code' => 'NOT_FOUND',
            'message' => 'Page not found',
        ]);
        return $response;
    }
}
