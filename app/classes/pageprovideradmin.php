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
            $html .= "<li class='providel-deals__deals-list-element'>{$this->normalLink('provider-deal', 'provider-deal', '/deal', $caption, $parameters)}</li>";
        }
        return $html;
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getHeader()}
        <h1 class='provider-main__title'>В работу</h1>
        <div class='provider-main__article provider-article'>
            <div class='provider-article__element provider-deals'>
                <h2 class='provider-deals__main-title'>Сделки, за которыми нет закрепленного сотрудника</h2>
                <h3 class='provider-deals__title'>Новые</h2>
                <ul class='provider-deals__deals-list'>{$this->getDeals($this->unpaidDealsProviderWithout)}</ul>
                <h3 class='provider-deals__title'>Оплаченные</h2>
                <ul class='provider-deals__deals-list'>{$this->getDeals($this->paidDealsProviderWithout)}</ul>
                <h3 class='provider-deals__title'>В которых есть непросмотренное сообщение</h2>
                <ul class='provider-deals__deals-list'>{$this->getDeals($this->notMessageSeenDealsProviderWithout)}</ul>
            </div>
            
            <div class='provider-article__element provider-deals'>
                <h2 class='provider-deals__main-title'>Ваши сделки</h2>
                <h3 class='provider-deals__title'>Неоплаченные</h2>
                <ul class='provider-deals__deals-list'>{$this->getDeals($this->unpaidDealsProviderWith)}</ul>
                <h3 class='provider-deals__title'>Оплаченные</h2>
                <ul class='provider-deals__deals-list'>{$this->getDeals($this->paidDealsProviderWith)}</ul>
                <h3 class='provider-deals__title'>В которых есть непросмотренные сообщения</h2>
                <ul class='provider-deals__deals-list'>{$this->getDeals($this->notMessageSeenDealsProviderWith)}</ul>
                <h3 class='provider-deals__title'>В работе</h2>
                <ul class='provider-deals__deals-list'>{$this->getDeals($this->beingDoneDealsProviderWith)}</ul>
            </div>
        </div>
        {$this->getFooter()}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}