<?php

namespace IndependentReserve\Concerns;

/**
 * Helper to sign messages to the API
 */
trait Authentication
{
    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiSecret;

    /**
     * Get the parameters required to make private API calls.
     *
     * @return array
     */
    protected function getAuthenticationParameters($url, array $params)
    {
        $nonce = str_pad(str_replace('.', '', microtime(true)), 19, 0);

        $params =  array_merge(['apiKey' => $this->apiKey, 'nonce' => $nonce], $params);

        $strToSign = $this->toUnsignedMessage($url, $params);

        $signature = strtoupper(hash_hmac('sha256', utf8_encode($strToSign), utf8_encode($this->apiSecret)));

        return [
            'apiKey' => $this->apiKey,
            'nonce' => $nonce,
            'signature' => $signature,
        ];
    }

    /**
     * Concert call to an unsigned message.
     *
     * The unsigned message can be used to create the signature required to authenticate calls to private methods from the API
     *
     * @param string $url
     * @param array $params
     */
    private function toUnsignedMessage($url, array $params) : string
    {
        $res = $url;
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $res .= ",$key=".array_values($value)[0]; // works only for case when array has one value. No reference in docs on how to use for array with more then one value
            } else {
                $res .= ",$key=$value";
            }
        }

        return $res;
    }
}
