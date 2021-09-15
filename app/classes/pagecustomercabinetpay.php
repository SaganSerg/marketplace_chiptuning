<?php
class PageCustomerCabinetPay extends PageCustomerCabinet
{
    protected $pageName = 'Pay';
    function __construct($name, $customer_id, $login, $coins)
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        parent::__construct($name, $customer_id, $login, $coins);
    }
    private function composeDictionaryMain() {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'BuyCoins' => [
                'en' => 'Buy coins',
                'ru' => 'Купить коины'
            ],
            'Pay' => [
                'en' => 'Pay',
                'ru' => 'Оплатить'
            ]
        ];
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getFacadeHeader()}
        {$this->getNavigation()}
        <article class='main__content content content_pay'>
            <h1 class='content__title'>{$this->getText($this->lang, 'BuyCoins')}</h1>
            <form class='content__block-buy-coins' method="post" action="{$this->getRequerMyself()}">
                <input type="number" name="coins">
                {$this->getSubmit($this->getText($this->lang, 'Pay'), $this->pageName)}
            </form>
        </article>
    {$this->getFacadeFooter()}
HTML;
    }
    function getHTML()
    {
        return $this->composeHTML();
    }
}