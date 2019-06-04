<?php

namespace IndependentReserve\Concerns;

/**
 * Helper to call private methods from the API
 */
trait PrivateMethods
{
    use Authentication;

    /**
     * @var array The available private methods os the API
     */
    protected $privateMethods = [
        'GetOpenOrders',
        'GetClosedOrders',
        'GetClosedFilledOrders',
        'GetOrderDetails',
        'GetAccounts',
        'GetTransactions',
        'GetDigitalCurrencyDepositAddress',
        'GetDigitalCurrencyDepositAddresses',
        'GetTrades',
        'GetBrokerageFees',
        'PlaceLimitOrder',
        'PlaceMarketOrder',
        'CancelOrder',
        'SynchDigitalCurrencyDepositAddressWithBlockchain',
        'RequestFiatWithdrawal',
        'WithdrawDigitalCurrency',
    ];

    /**
     * Place new market bid / offer order. A Market Bid is a buy order and a Market Offer is a sell order.
     *
     * @param string $primaryCurrencyCode The digital currency for which to retrieve market summary.
     *        Must be a valid primary currency, which can be checked via the
     *        getValidPrimaryCurrencyCodes() method.
     * @param string $secondaryCurrencyCode The fiat currency in which to retrieve market summary.
     *        Must be a valid secondary currency, which can be checked via the
     *        getValidSecondaryCurrencyCodes() method.
     * @param string $orderType The type of market order. Must be a valid market order type, which
     *        can be checked via the GetValidMarketOrderTypes method.
     * @param string $volume The volume to buy/sell in primary currency.
     *
     * @return \StdClass
     */
    public function placeMarketOrder(string $primaryCurrencyCode, string $secondaryCurrencyCode, string $orderType, $volume)
    {
        if (! in_array($orderType, $this->getValidMarketOrderTypes())) {
            throw new InvalidArgumentException("Order Type [$orderType] not supported");
        }

        return $this->callAPI(static::VISIBILITY_PRIVATE, 'PlaceMarketOrder', [
            'primaryCurrencyCode' => $primaryCurrencyCode,
            'secondaryCurrencyCode' => $secondaryCurrencyCode,
            'orderType' => $orderType,
            'volume' => $volume,
        ]);
    }

    /**
     * Retrieves details about a single order.
     *
     * @param string $orderGuid The guid of the order.
     *
     * @return \StdClass
     */
    public function getOrderDetails(string $orderGuid)
    {
        return $this->callAPI(static::VISIBILITY_PRIVATE, 'GetOrderDetails', [
            'orderGuid' => $orderGuid,
        ]);
    }
}
