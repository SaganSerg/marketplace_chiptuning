<?php
class PageProviderDeals extends PageProvider
{
    protected $pageName = '/deals';
    private $deals;
    private $messages;
    
    function __construct(string $name, array $deals, array $messages = null)
    {
        parent::__construct($name);
        $this->deals = $deals;
        $this->messages = $messages;
    }
    private function getMessages()
    {
        $html = '<div class="provider-gate__block-message">';
        if ($this->messages) {
            foreach($this->messages as $message) {
                if ($message['flag']) {
                    $html .= '<div  class="provider-gate__message">' . $message['text'] . '</div>';
                }
            }
        }
        return $html .= '</div>';
    }
    private function deals(array $deals)
    {
        $html = '';
        $classForm = 'provider-deal';
        $classButton = 'provider-deal';
        foreach ($deals as $deal) {
            
            $action = '/deal';
            $caption = 'ID клиента -- ' . $deal['customer_id'] . '. 
            ID сделки -- ' . $deal['customer_order_id'] . '.
            сумма сделки -- ' . $deal['customer_order_amount'] . '. 
            дата регистрации сделки -- ' . $deal['customer_order_date'] . '.
            cтатус сделки -- ' . $deal['customer_order_status'];
            $parameters = [
                'customer_order_id' => $deal['customer_order_id'],
                'Page' => $this->pageName
            ];
            $html .= "<li class='providel-deals__deals-list-element'>{$this->normalLink($classForm, $classButton, $action, $caption, $parameters)}</li>";
        }
        return $html;
    }
    private function today()
    {
        return time();
    } 
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getHeader()}
        <h1 class='provider-main__title'>Выбрать Ваши сделки</h1>
        {$this->getMessages()}
        <form class="provider-form-search" method="POST">
            <input type="hidden" name="Page" value="{$this->pageName}">
            <fieldset class="provider-form-search__inputs-block provider-inputs-block">
                <legend class="provider-inputs-block__title">Диапазон времени</legend>
                <input class="provider-inputs-block__input" type='date' name='datestart'>
                <input class="provider-inputs-block__input" type='date' name='dateend'>
            </fieldset>
            <fieldset class="provider-form-search__inputs-block provider-inputs-block">
                <legend class='provider-inputs-block__title'>Id клиента</legend>
                <input class="provider-inputs-block__input" type='number' name='customer_id'>
            </fieldset>
            <fieldset class="provider-form-search__inputs-block provider-inputs-block">
                <legend class='provider-inputs-block__title'>Статус сделки</legend>
                <select class='provider-inputs-block__select-block provider-select-block' name='customer_order_status[]' multiple>
                    <option class="provider-select-block__option" value="{$GLOBALS['paidDealStatus']}">Оплачена</option>
                    <option class="provider-select-block__option" value="{$GLOBALS['beingDoneDealStatus']}">В работе</option>
                    <option class="provider-select-block__option" value="{$GLOBALS['unpaidDealStatus']}">Не оплачена</option>
                    <option class="provider-select-block__option" value="{$GLOBALS['doneDealStatus']}">Выполнена</option>
                </select>
            </fieldset>
            <input class="provider-form-search__submit" type="submit" value="Выбранные сделки">
        </form>
        <form class="provider-form-search" method="POST">
            <input type="hidden" name="Page" value="{$this->pageName}">
            <input type="hidden" name='all_deals' value="all">
            <input class="provider-form-search__submit" type="submit" value="Все Ваши сделки">
        </form>
        <div class="provider-deals">
            <h2 class="provider-main__title">Ваши выбранные сделки</h2>
            <ul class='provider-deals__deals-list'>{$this->deals($this->deals)}</ul>
        </div>
        {$this->getFooter()}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}