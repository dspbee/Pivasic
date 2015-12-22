<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Core;

/**
 * Base functions to custom route.
 *
 * Class Route
 * @package Dspbee\Core
 */
class BaseRoute
{
    public function __construct()
    {
        $this->process = null;
    }

    /**
     * Get object of BaseProcess to handle the request.
     *
     * @return BaseController|null.
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @var BaseController|null
     */
    protected $process;
}