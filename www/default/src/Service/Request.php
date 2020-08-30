<?php

namespace App\Service;

class Request
{
    const
        REQUEST_METHOD_GET = 'GET',
        REQUEST_METHOD_POST = 'POST';

    /**
     * @return Request
     */
    public static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @param $page
     *
     * @return string
     */
    public static function NormalizeUri($page)
    {
        return rtrim($page, '/');
    }

    ####################################################################################################################

    /** @var string $url */
    private $url;

    /** @var string $method */
    private $method;

    /** @var null|array $data */
    private $data;

    /**
     * @throws \InvalidArgumentException
     */
    private function __construct()
    {
        $this->url = self::NormalizeUri(strtok($_SERVER["REQUEST_URI"], '?'));
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        if (null === $this->data) {
            if (self::REQUEST_METHOD_GET === $this->getMethod()) {
                $this->data = [];
            } else {
                $this->data = \json_decode(file_get_contents('php://input'), true);

                if (\JSON_ERROR_NONE !== \json_last_error()) {
                    throw new \InvalidArgumentException(
                        sprintf('Request data parsing error [%u]: %s', \json_last_error(), \json_last_error_msg())
                    );
                }
            }
        }

        return $this->data;
    }
}
