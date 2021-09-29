<?php
class PageCustomerCabinetDealsdeals extends PageCustomerCabinetDeals
{
    protected $deals;
    protected $pageName = '/dealsdeals';
    function __construct($name, $customer_id, $login, $coins, $deals)
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        $this->deals = $deals;
        parent::__construct($name, $customer_id, $login, $coins);
    }
    private function composeDictionaryMain() {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'unpaid' => [
                'en' => 'unpaid',
                'ru' => 'не оплачена'
            ],
            'paid' => [
                'en' => 'paid',
                'ru' => 'оплачена'
            ],
            'being done' => [
                'en' => 'being done',
                'ru' => 'делается'
            ],
            'done' => [
                'en' => 'done',
                'ru' => 'сделано'
            ],
            'unknown' => [
                'en' => 'unknown (some mistake)',
                'ru' => 'неизвестно (какая-то ошибка)'
            ],
            'ThisIsListOfYourTrades' => [
                'en' => 'This is a list of your trades',
                'ru' => 'Это список ваших сделок'
            ],
            'IndividualNumberOfYourDeal' => [
                'en' => 'Individual number of your deal',
                'ru' => 'Индивидуальный номер вашей сделки'
            ],
            'StatusOfYourDeal' => [
                'en' => 'The status of your deal',
                'ru' => 'Статус вашей сделки'
            ],
            'AmountOfYourTradeInCoins' => [
                'en' => 'The amount of your trade in coins',
                'ru' => 'Сумма вашей сделки в коинах'
            ]
        ];
    }
    private function getDealList()
    {
        $html = '';
        foreach ($this->deals as $deal) {
            $status = $this->transformDealStatus($deal['customer_order_status']);
            $attributes = [['name' => 'customer_order_id', 'value' => $deal['customer_order_id']]];
            $time = $this->transformTime($deal['customer_order_date']);
            $html .= "<li class='deals__element deal'><div class='deal__time'>$time</div><div class='deal__id'>{$this->getNormalLinkWithAttrubutes('/dealsdeal', '', $this->getText($this->lang, 'IndividualNumberOfYourDeal') . ' - ' . $deal['customer_order_id'] , $attributes)}</div><div class='deal__status'>{$this->getText($this->lang, 'StatusOfYourDeal')} -- <span class='deal__value-status deal__value-status_{$deal['customer_order_status']}'>{$this->getText($this->lang, $status)}</span></div><div class='deal__sum'>{$this->getText($this->lang, 'AmountOfYourTradeInCoins')} -- {$deal['customer_order_amount']}</div></li>";
        }
        return $html;
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getFacadeHeader()}
        {$this->getNavigation()}
        
        <article class="main__content content content_deals">
            <h1 class='content__title'>{$this->getText($this->lang, 'ThisIsListOfYourTrades')}</h1>
            <ul class='content__deals deals'>
                {$this->getDealList()}
            </ul>
        </article>
        {$this->getFacadeFooter()}
HTML;
    }
    function getHTML()
    {
        return $this->composeHTML();
    }
}