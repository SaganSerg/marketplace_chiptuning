<?php
abstract class PageCustomerCabinet extends PageCustomer
{
    protected $login;
    protected $coins;

    protected $sandwich = '<div class="page__hide-show hide-show" id="hide-show-button"></div>';
    function __construct($name, $customer_id, $login, $coins)
    {
        $this->dictionaryNavigation = $this->composeDictionaryNavigation();
        parent::__construct($name, $customer_id);
        $this->login = $login;
        $this->coins = $coins;
    }
    protected function getFacadeFooter()
    {
        return $this->footer();
    }
    protected function transformTime($time)
    {
        return gmdate("Y-m-d H:i:s", $time);
    }
    protected function getFacadeHeader()
    {
        return $this->header("page_cabinet", 'page__header_grey', $this->sandwich);
    }
    protected function composeDictionaryNavigation()
    {
        return [
            'AvailableCoins' => [
                'en' => 'Coins available: ',
                'ru' => 'Коинов в распоряжении: '
            ],
            'BuyCoins' => [
                'en' => 'Buy coins',
                'ru' => 'Купить коины'
            ],
            'OrderFileProcessing' => [
                'en' => 'Order file processing',
                'ru' => 'Заказть обработку файла'
            ],
            'Deals' => [
                'en' => 'Deals',
                'ru' => 'Сделки'
            ],
            'History' => [
                'en' => 'Payment history',
                'ru' => 'История оплат'
            ],
            "Profile" => [
                'en' => 'User data',
                'ru' => 'Данные пользователя'
            ]
        ];
    }
    protected function getNavigation()
    {
        $pages = [
            'pay' => 'BuyCoins', 
            'treatment' => 'OrderFileProcessing',
            'dealsdeals' => 'Deals',
            'history' => 'History',
            'profile' => 'Profile'
        ];
        $realPage = strtolower($this->pageName);
        $html = <<<HTML
            <nav class="page__nav main-nav page__nav_hide-show" id='main-nav'>
                <div class="main-nav__hide" id="main-nav-hide"></div>
                <div class="main-nav__login">$this->login</div>
                <div class="main-nav__coins coins">{$this->getText($this->lang, 'AvailableCoins')}<span class="coins__count">$this->coins</span></div>
                <ul class="main-nav__links">
HTML;
        $classBit = "main-nav__link_currant";
        foreach($pages as $pageName => $place) {
            $classBit = strtolower($this->pageName) == $pageName ? 'main-nav__link_currant' : '';
            $html .= "<li class='main-nav__item'>{$this->getNormalLink('/' . $pageName, 'main-nav__link ' . $classBit , $this->getText($this->lang, $place), $this->pageName)}</li>";
        }
        $html .=  '</ul></nav>';
        return $html;

    }
}