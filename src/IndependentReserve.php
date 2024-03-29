<?php

namespace IndependentReserve;

use InvalidArgumentException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use function GuzzleHttp\Psr7\str as psr7_str;

class IndependentReserve
{
    use Concerns\PublicMethods,
        Concerns\PrivateMethods;

    /*
    |--------------------------------------------------------------------------
    | Visibility Enum
    |--------------------------------------------------------------------------
    */
    const VISIBILITY_PUBLIC = 'Public';
    const VISIBILITY_PRIVATE = 'Private';

    /*
    |--------------------------------------------------------------------------
    | Account Status Enum
    |--------------------------------------------------------------------------
    */
    const ACCOUNT_STATUS_ACTIVE = 'Active';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var string The base uri for the API
     */
    protected static $baseUri = 'https://api.independentreserve.com';

    /**
     * @see https://api.independentreserve.com/Public/GetOrderMinimumVolumes
     *
     * @var array  The map of currency min volume
     */
    protected $volumeMin = [
        'Xbt' => 0.0001, 'Bch' => 0.001, 'Eth' => 0.001, 'Ltc' => 0.01,
        'Xrp' => 1, 'Zrx' => 1, 'Eos' => 0.1, 'Xlm' => 10, 'Bat' => 1,
        'Usdt' => 1, 'Etc' => 0.01, 'Link' => 0.1, 'Usdc' => 1, 'Mkr' => 0.001,
        'Dai' => 1, 'Comp' => 0.001, 'Snx' => 0, 'Yfi' => 0.0001, 'Aave' => 0.01,
        'Grt' => 1, 'Dot' => 0.1, 'Uni' => 0.1, 'Ada' => 1, 'Matic' => 0.1,
        'Doge' => 1, 'Sol' => 0.01, 'Mana' => 0.1, 'Sand' => 0.1,
    ];

    /**
     * @see https://api.independentreserve.com/Public/GetOrderVolumeDecimais
     *
     * @var array The map of currency decimals
     */
    protected $volumeDecimals = [
        'Xbt' => 8, 'Bch' => 8, 'Eth' => 8, 'Ltc' => 8,
        'Xrp' => 5, 'Zrx' => 4, 'Eos' => 4, 'Xlm' => 5, 'Bat' => 5,
        'Usdt' => 5, 'Etc' => 8, 'Link' => 4, 'Usdc' => 5, 'Mkr' => 8,
        'Dai' => 5, 'Comp' => 8, 'Snx' => 5, 'Yfi' => 8, 'Aave' => 5,
        'Grt' => 5, 'Dot' => 5, 'Uni' => 5, 'Ada' => 5, 'Matic' => 5,
        'Doge' => 5, 'Sol' => 5, 'Mana' => 5, 'Sand' => 5,
    ];

    /**
     * Constructor
     *
     * @param string|null $apiKey
     * @param string|null $apiSecret
     * @param \GuzzleHttp\Client $client
     */
    public function __construct($apiKey = null, $apiSecret = null, HttpClient $client = null, $baseUrl = null)
    {
        if (! is_null($apiKey) && ! is_null($apiSecret)) {
            $this->withAuthentication($apiKey, $apiSecret);
        }

        $this->client = $client ?? static::newHttpClient();

        if (! is_null($baseUrl)) {
            static::$baseUri = $baseUrl;
        }
    }

    /**
     * Create an http client with the proper configuration to call the API endpoints
     *
     * @return GuzzleHttp\Client
     */
    protected static function newHttpClient() : HttpClient
    {
        return new HttpClient([
            'base_uri' => static::$baseUri
        ]);
    }

    /**
     * Return a new IndependentReserve instance
     *
     * @param mixed $apiKey
     * @param mixed $apiSecret
     *
     * @return self
     */
    public static function instance($apiKey = null, $apiSecret = null, $baseUrl = null)
    {
        return new static(
            $apiKey,
            $apiSecret,
            static::newHttpClient(),
            $baseUrl
        );
    }

    /**
     * Handle dynamic method calls into the http client
     *
     * @param string $method
     * @param array  $params
     *
     * @return array
     */
    public function __call($method, $params)
    {
        $visibility = $this->getMethodVisibility($method);

        return $this->callAPI($visibility, ucfirst($method), $params);
    }

    /**
     * Call the independent reserve api
     *
     * @param mixed $visibility
     * @param mixed $method
     * @param array $params
     *
     * @return array|StdClass
     */
    protected function callAPI($visibility, $method, array $params = [])
    {
        if (! in_array($visibility, [static::VISIBILITY_PUBLIC, static::VISIBILITY_PRIVATE])) {
            throw new InvalidArgumentException("Invalid visibilty argument: [{$visibility}]");
        }

        $url = "/{$visibility}/".ucfirst($method);
        $query =  $visibility  === static::VISIBILITY_PUBLIC ? $params : [];
        $json = $visibility === static::VISIBILITY_PRIVATE
            ? ['json' => $this->getAuthenticationParameters(static::$baseUri.$url, $params) + $params]
            : [];

        try {
            $response = $this->client->request(
                $visibility === static::VISIBILITY_PUBLIC ? 'GET' : 'POST',
                $url,
                ['query' => $query] + $json
            );
        } catch (ClientException $e) {
            throw new Exceptions\IndependentReserveException(
                json_decode($e->getResponse()->getBody())->Message ?? psr7_str($e->getResponse())
            );
        }

        return json_decode($response->getBody());
    }

    /**
     * Return if the fiven api method is a public or private method
     *
     * @param string $method
     *
     * @return string
     *
     * @throws \InvalidArgumentException when the method is invalid
     */
    protected function getMethodVisibility($method)
    {
        if (in_array(ucfirst($method), $this->publicMethods ?? [])) {
            return static::VISIBILITY_PUBLIC;
        }

        if (in_array(ucfirst($method), $this->privateMethods ?? [])) {
            return static::VISIBILITY_PRIVATE;
        }

        throw new InvalidArgumentException("The method [{$method}] does not exists in the API");
    }

    /**
     * Return the min volume to use when dealing with the given currency pair
     *
     * @param mixed $currency
     *
     * @return float
     */
    public function getMinVolumeFor($currency)
    {
        if (isset($this->volumeMin[$currency])) {
            return $this->volumeMin[$currency];
        }

        throw new InvalidArgumentException("Min Volume not available for the given currency [$currency]");
    }

    /**
     * Return the digits to be used on volume for a given curency
     *
     * @param mixed $currency
     *
     * @return float
     */
    public function getVolumeDecimalsFor($currency)
    {
        if (isset($this->volumeDecimals[$currency])) {
            return $this->volumeDecimals[$currency];
        }

        throw new InvalidArgumentException("Digits Volume not available for the given currency [$currency]");
    }
}
