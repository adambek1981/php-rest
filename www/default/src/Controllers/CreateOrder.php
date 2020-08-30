<?php

namespace App\Controllers;

use App\Repository\Orders;
use App\Repository\Products;
use App\Service\Request;
use App\Service\Response;
use App\Service\Validator;
use Symfony\Component\Validator\Constraints as Assert;

class CreateOrder implements ControllerInterface
{
    /**
     * @inheritDoc
     *
     * @throws \UnexpectedValueException
     * @throws \LogicException
     */
    public function process(): Response
    {
        $this->validateRequest();

        $orderNumber = Orders::createNewOrder(Request::getInstance()->getData()['products']);

        $response = new Response(200);
        $response->setContent([
            'code' => 'ORDER_CREATED',
            'message' => 'The new order created successfully',
            'order_number' => $orderNumber,
        ]);
        return $response;
    }

    /**
     * @throws \UnexpectedValueException
     * @throws \LogicException
     */
    private function validateRequest()
    {
        $validator = new Validator();
        $validator->validate(
            Request::getInstance()->getMethod(),
            new Assert\EqualTo(Request::REQUEST_METHOD_POST)
        );
        $validator->validate(
            Request::getInstance()->getData(),
            new Assert\Collection([
                'fields' => [
                    'products' => new Assert\Required([
                        new Assert\NotBlank(),
                        new Assert\Type('array'),
                    ]),
                ],
            ]),
        );

        foreach (Request::getInstance()->getData()['products'] as $product) {
            $validator->validate($product, new Assert\Type('integer'));
        }

        $notExisted = Products::getNotExisted(Request::getInstance()->getData()['products']);
        if (!empty($notExisted)) {
            throw new \LogicException('Unknown product ids: ' . implode(', ', $notExisted));
        }
    }
}
