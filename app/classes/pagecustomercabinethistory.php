<?php
class PageCustomerCabinetHistory extends PageCustomerCabinet
{
    protected $pageName = '/history';
    private $buyList;
    function __construct($name, $customer_id, $email, $coins, $buyList)
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        $this->buyList = $buyList;
        parent::__construct($name, $customer_id, $email, $coins);
    }
    private function composeDictionaryMain() {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'PayTime' => [
                'en' => 'Transaction time',
                'ru' => 'Время транзакции'
            ],
            'PaymentAmount' => [
                'en' => 'Transaction amount ',
                'ru' => 'Сумма транзакции'
            ],
            'TransactionList' => [
                'en' => 'Transaction List',
                'ru' => 'Список транзакций'
            ]
        ];
    }
    private function list()
    {
        $html = '';
        foreach ($this->buyList as $elem) {
            $html .= "<li class='transactions__transaction transaction'>
                <div class='transaction__time'>{$this->getText($this->lang, 'PayTime')} <span class='transaction__time-value'>{$this->transformTime($elem['coin_transaction_date'])}</span></div>
                <div class='transaction__sum'>{$this->getText($this->lang, 'PaymentAmount')} <span class='transaction__sum-value'>{$elem['coin_transaction_sum']}</span></div>
            </li>";
        }
        return $html;
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getFacadeHeader()}
        {$this->getNavigation()}
        <article class="main__content content content_history">
            <h1 class="content__title">{$this->getText($this->lang, 'TransactionList')}</h1>
            <ul class='content__transactions transactions'>
                {$this->list()}
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