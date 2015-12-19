<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Bundle\Common\Bag;

/**
 * Class CookieBag
 * @package Dspbee\Bundle\Common\Bag
 */
class CookieBag extends ValueBag
{
    /**
     * @param array $bag
     */
    public function add(array $bag = [])
    {
        $_COOKIE = array_replace($_COOKIE, $bag);
    }

    /**
     * Returns true if the COOKIE parameter is defined.
     *
     * @param string $key The key
     *
     * @return bool true if the parameter exists, false otherwise
     */
    public function has($key)
    {
        return array_key_exists($key, $_COOKIE);
    }

    /**
     * Returns the COOKIE keys.
     *
     * @return array An array of parameter keys
     */
    public function keys()
    {
        return array_keys($_COOKIE);
    }

    /**
     * Returns a COOKIE parameter by name.
     *
     * @param string $key
     * @param mixed|null $default The default value if the parameter key does not exist
     *
     * @return mixed|null
     */
    public function fetch($key, $default = null)
    {
        return $_COOKIE[$key] ?? $default;
    }

    /**
     * Returns the number of values.
     *
     * @return int
     */
    public function count()
    {
        return count($_COOKIE);
    }
}