<?php
class PageProviderGate extends PageProvider
{
    protected $pageName = '/gate';
    protected $showLink = false;
    protected $error;
    
    function __construct(string $name, bool $error = false)
    {
        parent::__construct($name);
        $this->error = $error;
    }
    function message()
    {
        if ($this->error) return '<div>Ошибка</div>';
    }
    
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getHeader()}
        {$this->message()}
        <form method="POST" action='/admin'>
            Логин
            <input type='text' name='login'>
            Пароль
            <input type='password' name='pass'>
            <input type='hidden' value='{$this->pageName}' name='Page'> 
            <input type='submit' value='Войти'>
        </form>
        {$this->getFooter()}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}