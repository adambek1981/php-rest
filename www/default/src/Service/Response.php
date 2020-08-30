<?php

namespace App\Service;

class Response
{
    const
        TYPE_JSON = 'json',
        TYPE_HTML = 'html';

    ####################################################################################################################

    /** @var int $responseCode */
    private $responseCode;

    /** @var string $contentType */
    private $contentType;

    /** @var null|array $error */
    private $error;

    /** @var null|array $content */
    private $content;

    /**
     * @param int $responseCode
     */
    public function __construct(int $responseCode)
    {
        $this->responseCode = $responseCode;
        $this->contentType =
            (Request::REQUEST_METHOD_GET === Request::getInstance()->getMethod())
                ? self::TYPE_HTML
                : self::TYPE_JSON;
    }

    /**
     * @param \Throwable $exception
     */
    public function setException(\Throwable $exception)
    {
        $this->setError([
            'code' => 'ERROR',
            'message' => sprintf('[%s] %s', get_class($exception), $exception->getMessage()),
            'debug' => $exception->getTrace(),
        ]);
    }

    /**
     * @param array $data
     */
    public function setError(array $data)
    {
        $this->error = $data;
    }

    /**
     * @param array $data
     */
    public function setContent(array $data)
    {
        $this->content = $data;
    }

    /**
     * @return void
     */
    public function sendToStream()
    {
        http_response_code($this->responseCode);

        if (self::TYPE_JSON === $this->contentType) {
            header('Content-Type: application/json; charset=utf-8');
            echo \json_encode([
                'error' => $this->error,
                'content' => $this->content,
            ]);
        } else {
            $html  = '<!DOCTYPE html><head><title>REST API (json)</title></head><body>';
            if (null !== $this->content) {
                $html .= sprintf('%s: %s', $this->content['code'], $this->content['message']);
            }
            if (null !== $this->error) {
                $html .= sprintf('<h3>%s</h3><p>%s</p>', $this->error['code'], $this->error['message']);
                if (isset($this->error['debug'])) {
                    $html .= sprintf(
                        '<plaintext>%s</plaintext>',
                        \json_encode($this->error['debug'], \JSON_PRETTY_PRINT)
                    );
                }
            }
            $html .= '</body></html>';

            echo $html;
        }
    }
}
