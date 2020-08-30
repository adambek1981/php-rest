<?php

namespace App\Controllers;

use App\Repository\Orders;
use App\Service\Request;
use App\Service\Response;
use App\Service\Validator;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Validator\Constraints as Assert;

class PayOrder implements ControllerInterface
{
    /**
     * @inheritDoc
     */
    public function process(): Response
    {
        $this->validateRequest();
        $orderNumber = Request::getInstance()->getData()['order_number'];
        $httpClient = new Client();

        $yaResponse = $httpClient->request(
            Request::REQUEST_METHOD_POST,
            'http://ya.ru/',
            [
                RequestOptions::HTTP_ERRORS => false,
            ]
        );

        $response = new Response(200);

        if (200 === $yaResponse->getStatusCode()) {
            Orders::payOrder($orderNumber);

            $response->setContent([
                'code' => 'ORDER_PAYED',
                'message' => 'The order payed successfully',
                'order_number' => $orderNumber,
            ]);
        } else {
            $response->setContent([
                'code' => 'DECLINE',
                'message' => 'The order not payed',
                'order_number' => $orderNumber,
            ]);
        }

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
                'allowExtraFields' => true,
                'fields' => [
                    'order_number' => new Assert\Required([
                        new Assert\NotBlank(),
                        new Assert\Type('string'),
                    ]),
                    'amount' => new Assert\Required([
                        new Assert\NotBlank(),
                        new Assert\Type('integer'),
                    ]),
                ],
            ]),
        );
        $orderNumber = Request::getInstance()->getData()['order_number'];

        if (!Orders::findOrderWithStatusNew($orderNumber)) {
            throw new \LogicException(sprintf('Not found ORDER="%s"', $orderNumber));
        }

        $validator->validate(
            Request::getInstance()->getData()['amount'],
            new Assert\EqualTo(Orders::getOrderAmount($orderNumber)),
        );
    }
}
