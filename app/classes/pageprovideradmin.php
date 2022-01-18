<?php
class PageProviderAdmin extends PageProvider
{
    protected $pageName = '/admin';
    private $unpaidDealsProviderWithout;
    private $paidDealsProviderWithout;
    private $notMessageSeenDealsProviderWithout;
    private $unpaidDealsProviderWith;
    private $paidDealsProviderWith;
    private $notMessageSeenDealsProviderWith;
    private $beingDoneDealsProviderWith;
    function __construct(
        string $name,
        array $unpaidDealsProviderWithout, 
        array $paidDealsProviderWithout,
        array $notMessageSeenDealsProviderWithout,
        array $unpaidDealsProviderWith,
        array $paidDealsProviderWith,
        array $notMessageSeenDealsProviderWith,
        array $beingDoneDealsProviderWith)
    {
        parent::__construct($name);
        $this->unpaidDealsProviderWithout = $unpaidDealsProviderWithout;
        $this->paidDealsProviderWithout = $paidDealsProviderWithout;
        $this->notMessageSeenDealsProviderWithout = $notMessageSeenDealsProviderWithout;
        $this->unpaidDealsProviderWith = $unpaidDealsProviderWith;
        $this->paidDealsProviderWith = $paidDealsProviderWith;
        $this->notMessageSeenDealsProviderWith = $notMessageSeenDealsProviderWith;
        $this->beingDoneDealsProviderWith = $beingDoneDealsProviderWith;
    }
    private function getDeals(array $deals)
    {
        $html ='';
        foreach ($deals as $deal) {
            $caption = 'ID клиента -- ' . $deal['customer_id'] . '. ID сделки -- ' . $deal['customer_order_id'] . '.';
            $parameters = [
                'customer_order_id' => $deal['customer_order_id'],
                'Page' => $this->pageName
            ];
            $html .= "<li>{$this->normalLink('', '', '/deal', $caption, $parameters)}</li>";
        }
        return $html;
    }
    // private function lkjlkj()
    // {
    //     echo '<pre>';
    //     print_r($this->unpaidDealsProviderWithout);
    //     print_r($this->paidDealsProviderWithout);
    //     print_r($this->notMessageSeenDealsProviderWithout);
    //     print_r($this->unpaidDealsProviderWith);
    //     print_r($this->paidDealsProviderWith);
    //     print_r($this->notMessageSeenDealsProviderWith);
    //     print_r($this->beingDoneDealsProviderWith);
    //     echo '</pre>';
    // }
    // private function newDeals(array $newDeals) {
    //     $html = '';
    //     $classForm = '';
    //     $classButton = '';
    //     foreach ($newDeals as $deal) {
    //         $action = '/deal';
    //         $caption = 'ID клиента -- ' . $deal['customer_id'] . '. ID сделки -- ' . $deal['order_id'] . '.';
    //         $parameters = [
    //             'order_id' => $deal['order_id'],
    //             'Page' => $this->pageName
    //         ];
    //         $html .= "<li>{$this->normalLink($classForm, $classButton, $action, $caption, $parameters)}</li>";
    //     }
    //     return $html;
    // }
    // private function paidDeals(array $paidDeals)
    // {
    //     $html = '';
    //     $classForm = '';
    //     $classButton = '';
    //     foreach ($paidDeals as $deal) {
    //         $action = '/deal';
    //         $caption = 'ID клиента -- ' . $deal['customer_id'] . '. ID сделки -- ' . $deal['order_id'] . '.';
    //         $parameters = [
    //             'order_id' => $deal['order_id'],
    //             'Page' => $this->pageName
    //         ];
    //         $html .= "<li>{$this->normalLink($classForm, $classButton, $action, $caption, $parameters)}</li>";
    //     }
    //     return $html;
    // }
    // private function beingDoneDeals(array $beingDoneDeals)
    // {
    //     $html = '';
    //     $classForm = '';
    //     $classButton = '';
    //     foreach ($beingDoneDeals as $deal) {
    //         $action = '/deal';
    //         $caption = 'ID клиента -- ' . $deal['customer_id'] . '. ID сделки -- ' . $deal['order_id'] . '.';
    //         $parameters = [
    //             'order_id' => $deal['order_id'],
    //             'Page' => $this->pageName
    //         ];
    //         $html .= "<li>{$this->normalLink($classForm, $classButton, $action, $caption, $parameters)}</li>";
    //     }
    //     return $html;
    // }
    // private function notMessageSeenDeals($notMessageSeenDeals)
    // {
    //     $html = '';
    //     $classForm = '';
    //     $classButton = '';
    //     foreach ($notMessageSeenDeals as $deal) {
    //         $action = '/deal';
    //         $caption = 'ID сделки -- ' . $deal['order_id'] . '.';
    //         $parameters = [
    //             'order_id' => $deal['order_id'],
    //             'Page' => $this->pageName
    //         ];
    //         $html .= "<li>{$this->normalLink($classForm, $classButton, $action, $caption, $parameters)}</li>";
    //     }
    //     return $html;
    // }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getHeader()}
        <h1>В работу</h1>
        <div>
            <div>
                <h2>Новые сделки, за которыми нет закрепленного сотрудника</h2>
                <ul>{$this->getDeals($this->unpaidDealsProviderWithout)}</ul>
            </div>
            <div>
                <h2>Оплаченные сделки, за которыми нет закрепленного сотрудника</h2>
                <ul>{$this->getDeals($this->paidDealsProviderWithout)}</ul>
            </div>
            <div>
                <h2>Сделки за которыми нет закрепленного сотрудника, в которых есть непросмотренное сообщение</h2>
                <ul>{$this->getDeals($this->notMessageSeenDealsProviderWithout)}</ul>
            </div>
            <div>
                <h2>Ваши неоплаченные сделки</h2>
                <ul>{$this->getDeals($this->unpaidDealsProviderWith)}</ul>
            </div>
            <div>
                <h2>Ваши оплаченные сделки</h2>
                <ul>{$this->getDeals($this->paidDealsProviderWith)}</ul>
            </div>
            <div>
                <h2>Ваши сделки, в которых есть непросмотренные сообщения</h2>
                <ul>{$this->getDeals($this->notMessageSeenDealsProviderWith)}</ul>
            </div>
            <div>
                <h2>Ваши сделки в работе</h2>
                <ul>{$this->getDeals($this->beingDoneDealsProviderWith)}</ul>
            </div>
        </div>
        {$this->getFooter()}
    
HTML;
// $this->lkjlkj();
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}