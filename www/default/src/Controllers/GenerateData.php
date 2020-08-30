<?php

namespace App\Controllers;

use App\Entity\Product;
use App\Repository\Products;
use App\Service\Request;
use App\Service\Response;
use App\Service\Validator;
use Symfony\Component\Validator\Constraints as Assert;

class GenerateData implements ControllerInterface
{
    /**
     * @return Response
     *
     * @throws \UnexpectedValueException
     */
    public function process(): Response
    {
        $this->validateRequest();

        $products = [];
        for ($index = 1; $index <= 20; $index++) {
            $products[] = Product::generate();
        }
        Products::store($products);

        $response = new Response(200);
        $response->setContent([
            'code' => 'GENERATED_SUCCESS',
            'message' => 'Data is generated successfully',
        ]);
        return $response;
    }

    /**
     * @throws \UnexpectedValueException
     */
    private function validateRequest()
    {
        $validator = new Validator();
        $validator->validate(
            Request::getInstance()->getMethod(),
            new Assert\EqualTo(Request::REQUEST_METHOD_POST)
        );
    }
}
