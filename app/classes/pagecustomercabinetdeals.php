<?php
abstract class PageCustomerCabinetDeals extends PageCustomerCabinet
{
    
    function __construct($name, $customer_id, $login, $coins)
    {
        parent::__construct($name, $customer_id, $login, $coins);
    }
    protected function transformDealStatus($status)
    {
        switch ($status) {
            case 'unpaid': return'unpaid';
            case 'paid' : return 'paid';
            case 'being_done' : return 'being done';
            case 'done' : return 'done';
            default : return 'unknown'; 
        }
    }
}