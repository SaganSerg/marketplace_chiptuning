<?php
class PageCustomerCabinetDownloadedfile extends PageCustomerCabinet
{
    private $order_id;
    protected $pageName = 'Downloadedfile';
    function __construct($name, $login, $coins, $order_id)
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        $this->order_id;
        parent::__construct($name, $login, $coins);
    }
    private function composeDictionaryMain() {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ]
        ];
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getFacadeHeader()}
        {$this->getNavigation()}
        
        <article class="main__content content content_file-downloaded">
            <h1 class="content__title">Файл загружен</h1>
            <div>Стоимость наших услуг составляет 120 коинов</div>
            <div>В вашем распоряжении имеется {$this->coins} коинов</div>
            <div>Если коинов не хватает, Вы можете дополнительно докупить коины</div>
        </article>
        {$this->getFacadeFooter()}
HTML;
    }
    function getHTML()
    {
        return $this->composeHTML();
    }
}