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
        if ($this->error) return '<div class="provider-gate__message">Ошибка!</div>';
    }
    
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getHeader()}
        {$this->message()}
        <form class="provider-gate-form" method="POST" action='/admin'>
            <div class="provider-gate-form__label">Логин</div>
            <input class='provider-gate-form__input' type='text' name='login'>
            <div class="provider-gate-form__label">Пароль</div>
            <input class='provider-gate-form__input' type='password' name='pass'>
            <input type='hidden' value='{$this->pageName}' name='Page'> 
            <input class="provider-gate-form__submit" type='submit' value='Войти'>
        </form>
        {$this->getFooter()}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}