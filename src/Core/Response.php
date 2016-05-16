<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Core;

/**
 * Represents a HTTP response.
 *
 * Class Response
 * @package Dspbee\Core
 */
class Response
{
    public function __construct()
    {
        $this->content = '';
    }

    /**
     * Send response.
     */
    public function send()
    {
        echo $this->content;

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif ('cli' !== php_sapi_name()) {
            $level = ob_get_level();
            if (0 < $level) {
                $status = ob_get_status(true);
                // PHP_OUTPUT_HANDLER_* are not defined on HHVM 3.3
                $flags = defined('PHP_OUTPUT_HANDLER_REMOVABLE') ? PHP_OUTPUT_HANDLER_REMOVABLE | PHP_OUTPUT_HANDLER_FLUSHABLE : -1;
                while ($level-- > 0 && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || $flags === ($s['flags'] & $flags) : $s['del'])) {
                    ob_end_flush();
                }
            }
        }
    }

    /**
     * Set response body.
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get response body.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set response header.
     *
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public function header($name, $value)
    {
        if (!empty($name) && !empty($value) && !headers_sent()) {
            header($name . ': ' . $value);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function setContentTypeJson()
    {
        return $this->header('Content-Type', 'application/json; charset=utf-8');
    }

    /**
     * @return bool
     */
    public function setContentTypeXml()
    {
        return $this->header('Content-Type', 'text/xml; charset=utf-8');
    }

    /**
     * Set response status.
     *
     * @param integer $statusCode  - status
     * @param string $version      - HTTP version
     * @param null $statusText     - status text
     *
     * @return bool
     */
    public function headerStatus($statusCode, $version = '1.1', $statusText = null)
    {
        if (!headers_sent()) {
            $statusCode = (int) $statusCode;

            if (null === $statusText) {
                $statusTexts = [
                    100 => 'Continue',
                    101 => 'Switching Protocols',
                    102 => 'Processing',            // RFC2518
                    200 => 'OK',
                    201 => 'Created',
                    202 => 'Accepted',
                    203 => 'Non-Authoritative Information',
                    204 => 'No Content',
                    205 => 'Reset Content',
                    206 => 'Partial Content',
                    207 => 'Multi-Status',          // RFC4918
                    208 => 'Already Reported',      // RFC5842
                    226 => 'IM Used',               // RFC3229
                    300 => 'Multiple Choices',
                    301 => 'Moved Permanently',
                    302 => 'Found',
                    303 => 'See Other',
                    304 => 'Not Modified',
                    305 => 'Use Proxy',
                    307 => 'Temporary Redirect',
                    308 => 'Permanent Redirect',    // RFC7238
                    400 => 'Bad Request',
                    401 => 'Unauthorized',
                    402 => 'Payment Required',
                    403 => 'Forbidden',
                    404 => 'Not Found',
                    405 => 'Method Not Allowed',
                    406 => 'Not Acceptable',
                    407 => 'Proxy Authentication Required',
                    408 => 'Request Timeout',
                    409 => 'Conflict',
                    410 => 'Gone',
                    411 => 'Length Required',
                    412 => 'Precondition Failed',
                    413 => 'Payload Too Large',
                    414 => 'URI Too Long',
                    415 => 'Unsupported Media Type',
                    416 => 'Range Not Satisfiable',
                    417 => 'Expectation Failed',
                    418 => 'I\'m a teapot',                                               // RFC2324
                    422 => 'Unprocessable Entity',                                        // RFC4918
                    423 => 'Locked',                                                      // RFC4918
                    424 => 'Failed Dependency',                                           // RFC4918
                    425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
                    426 => 'Upgrade Required',                                            // RFC2817
                    428 => 'Precondition Required',                                       // RFC6585
                    429 => 'Too Many Requests',                                           // RFC6585
                    431 => 'Request Header Fields Too Large',                             // RFC6585
                    500 => 'Internal Server Error',
                    501 => 'Not Implemented',
                    502 => 'Bad Gateway',
                    503 => 'Service Unavailable',
                    504 => 'Gateway Timeout',
                    505 => 'HTTP Version Not Supported',
                    506 => 'Variant Also Negotiates (Experimental)',                      // RFC2295
                    507 => 'Insufficient Storage',                                        // RFC4918
                    508 => 'Loop Detected',                                               // RFC5842
                    510 => 'Not Extended',                                                // RFC2774
                    511 => 'Network Authentication Required',                             // RFC6585
                ];
                $statusText = $statusTexts[$statusCode];
            }

            header(sprintf('HTTP/%s %s %s', $version, $statusCode, $statusText), true, $statusCode);
            return true;
        }

        return false;
    }

    /**
     * Redirect to url with statusCode and terminate.
     *
     * @param null|string $url
     * @param int $statusCode
     */
    public function redirect($url = null, $statusCode = 302)
    {
        $server = filter_input_array(INPUT_SERVER);
        if (null === $url && isset($server['REQUEST_URI'])) {
            $url = '/' . trim($server['REQUEST_URI'], '/');
            $url = filter_var($url, FILTER_VALIDATE_URL);
        }

        if (headers_sent()) {
            echo sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="0;url=%1$s" />
        <title>Redirecting to %1$s</title>
    </head>
    <body>
        <script type="text/javascript"> window.location.href = "%1$s"; </script>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));

        } else {
            header('Location: ' . $url, true, $statusCode);

            echo sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="1;url=%1$s" />
        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));

        }
    }

    private $content;
}