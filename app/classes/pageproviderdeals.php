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
        $html = '<div>';
        if ($this->messages) {
            foreach($this->messages as $message) {
                if ($message['flag']) {
                    $html .= '<div>' . $message['text'] . '</div>';
                }
            }
        }
        return $html .= '</div>';
    }
    private function deals(array $deals)
    {
        $html = '';
        $classForm = '';
        $classButton = '';
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
            $html .= "<li>{$this->normalLink($classForm, $classButton, $action, $caption, $parameters)}</li>";
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
        <h1>Все ваши сделки</h1>
        {$this->getMessages()}
        <form method="POST">
            <input type="hidden" name="Page" value="{$this->pageName}">
            <fieldset>
                <legend>Диапазон времени</legend>
                <input type='date' name='datestart'>
                <input type='date' name='dateend'>
            </fieldset>

            <input type='number' name='customer_id'>

            <select name='customer_order_status[]' multiple>
                <option value="{$GLOBALS['paidDealStatus']}">Оплачена</option>
                <option value="{$GLOBALS['beingDoneDealStatus']}">В работе</option>
                <option value="{$GLOBALS['unpaidDealStatus']}">Не оплачена</option>
                <option value="{$GLOBALS['doneDealStatus']}">Выполнена</option>
            </select>
            <input type="submit" value="оправить">
        </form>
        <form method="POST">
            <input type="hidden" name="Page" value="{$this->pageName}">
            <input type="hidden" name='all_deals' value="all">
            <input type="submit" value="все сделки">
        </form>
        <div>
            <ul>{$this->deals($this->deals)}</ul>
        </div>
        {$this->getFooter()}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}