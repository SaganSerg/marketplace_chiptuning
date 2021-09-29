<?php
class PageCustomerFacadeSentmail extends PageCustomerFacade
{
    protected $pageName = '/sentmail';
    protected const INPUT_ATTRIBUTE_NAME = [];
    function __construct(
        string $name,
        $customer_id
        )
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
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
            'Letter sent' => [
                'en' => 'Letter sent',
                'ru' => 'Письмо направлено'
            ],
            'A link to a page where you can change your password has been sent to your e-mail' => [
                'en' => 'A link to a page where you can change your password has been sent to your e-mail',
                'ru' => 'На Вашу электронную почту направлена ссылка на страницу, на которой Вы сможете изменить пароль'
            ],
            'Check your email inbox. If you do not find emails, be sure to check for spam' => [
                'en' => 'Check your email inbox. If you do not find emails, be sure to check for spam',
                'ru' => 'Проверьте ящик вашей электронной почты. Если Вы не обнаружите письма, обязательно проверьте спам'
            ]
        ];
    }
    
    private function composeHTML() 
    {
        $hiddenInputs = $this->getComposedHiddenInputs();
        return <<<HTML
        {$this->getFacadeHeader('contacts', $hiddenInputs)}
        <article class='main__facade-article facade-article'>
            <h1 class='facade-article__title'>{$this->getText($this->lang, 'Letter sent')}</h1>
            <p class='facade-article__paragraph'>{$this->getText($this->lang, 'A link to a page where you can change your password has been sent to your e-mail')}</p>
            <p class='facade-article__paragraph'>{$this->getText($this->lang, 'Check your email inbox. If you do not find emails, be sure to check for spam')}</p>
        </article>
       
        {$this->getFacadeFooter($this->pageName, $hiddenInputs)}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}