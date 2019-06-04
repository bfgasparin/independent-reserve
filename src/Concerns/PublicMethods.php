<?php

namespace IndependentReserve\Concerns;

/**
 * Helper to call public methods from the API
 */
trait PublicMethods
{
    /**
     * @var array The available public methods os the API
     */
    protected $publicMethods = [
        'GetValidPrimaryCurrencyCodes',
        'GetValidSecondaryCurrencyCodes',
        'GetValidLimitOrderTypes',
        'GetValidMarketOrderTypes',
        'GetValidOrderTypes',
        'GetValidTransactionTypes',
        'GetMarketSummary',
        'GetOrderBook',
        'GetAllOrders',
        'GetTradeHistorySummary',
        'GetRecentTrades',
        'GetFxRates',
    ];

    /**
     * Returns a current snapshot of the Independent Reserve market for a given currency pair.
     *
     * @param string $primaryCurrencyCode The digital currency for which to retrieve market summary.
     *        Must be a valid primary currency, which can be checked via the
     *        getValidPrimaryCurrencyCodes() method.
     * @param string $secondaryCurrencyCode The fiat currency in which to retrieve market summary.
     *        Must be a valid secondary currency, which can be checked via the
     *        getValidSecondaryCurrencyCodes() method.
     *
     * @return StdClass
     */
    public function getMarketSummary(string $primaryCurrencyCode, string $secondaryCurrencyCode)
    {
        return $this->callAPI(static::VISIBILITY_PUBLIC, 'GetMarketSummary', [
            'primaryCurrencyCode' => $primaryCurrencyCode,
            'secondaryCurrencyCode' => $secondaryCurrencyCode,
        ]);
    }


    /**
     * Returns the Order Book for a given currency pair.
     *
     * @param string $primaryCurrencyCode The cryptocurrency for which to retrieve order book.
     *        Must be a valid primary currency, which can be checked via the GetValidPrimaryCurrencyCodes method
     * @param string $secondaryCurrencyCode The fiat currency in which to retrieve order book.
     *        Must be a valid secondary currency, which can be checked via the GetValidSecondaryCurrencyCodes method
     *
     * @return array
     */
    public function getOrderBook(string $primaryCurrencyCode, string $secondaryCurrencyCode)
    {
        return $this->callAPI(static::VISIBILITY_PUBLIC, 'GetOrderBook', [
            'primaryCurrencyCode' => $primaryCurrencyCode,
            'secondaryCurrencyCode' => $secondaryCurrencyCode,
        ]);
    }
}
