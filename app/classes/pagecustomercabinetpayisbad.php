<?php
class PageCustomerCabinetPayisbad extends PageCustomerCabinet
{
    protected $pageName = '/payisbad';
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

        ];
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getFacadeHeader()}
        {$this->getNavigation()}
        оплата не прошла
    {$this->getFacadeFooter()}
HTML;
    }
    function getHTML()
    {
        return $this->composeHTML();
    }
}