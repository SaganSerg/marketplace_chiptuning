<?php
class PageCustomerFacadeMessagesentmailregistration extends PageCustomerFacade
{
    protected $pageName = '/messagesentmailregistration';
    private $email_for_registration_email;
    protected const INPUT_ATTRIBUTE_NAME = [];
    function __construct(
        string $name,
        $customer_id,
        $email_for_registration_email
        )
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        $this->email_for_registration_email = $email_for_registration_email;
        parent::__construct($name, $customer_id);    
    }
    
    private function composeDictionaryMain() 
    {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'CookiesAgreeMain' => [
                'en' => 'You must give your consent to the use of cookies',
                'ru' => 'Вы должны дать свое согласие на использование файлов-cookie'
            ],
            'Сontacts' => [
                'en' => 'Contacts',
                'ru' => 'Контакты'
            ]
        ];
    }
    
    private function composeHTML() 
    {
        $hiddenInputs = $this->getComposedHiddenInputs();
        return <<<HTML
        {$this->getFacadeHeader('contacts', $hiddenInputs)}
        <article class='main__facade-article facade-article'>
            <div>Мы отправили сообщение на почту <span>$this->email_for_registration_email</span></div>
        </article>
       
        {$this->getFacadeFooter($this->pageName, $hiddenInputs)}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}