<?php
class PageProviderNoright extends PageProvider
{
    protected $pageName = '/noright';

    function __construct(string $name)
    {
        parent::__construct($name);
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getHeader()}
        <div>У Вас нет прав работать с данной сделкой</div>
        {$this->getFooter()}
    
HTML;

    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}