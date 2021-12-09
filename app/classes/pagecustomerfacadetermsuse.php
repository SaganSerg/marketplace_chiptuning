<?php
class PageCustomerFacadeTermsuse extends PageCustomerFacade
{
    protected $pageName = '/termsuse';
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
            'СonditionsProcessingPersonalData' => [
                'en' => 'Conditions for processing personal data',
                'ru' => 'Условия обработки персональных данных'
            ]
        ];
    }
    
    private function composeHTML() 
    {
        $hiddenInputs = $this->getComposedHiddenInputs();
        return <<<HTML
        {$this->getFacadeHeader('termsuse', $hiddenInputs)}
        <article class='main__facade-article facade-article'>
            <h1 class='facade-article__title'>{$this->getText($this->lang, 'СonditionsProcessingPersonalData')}</h1>
            <p class='facade-article__paragraph'>Это первый пункт</p>
            <p class='facade-article__paragraph'>Это второй пункт</p>
        </article>
       
        {$this->getFacadeFooter($this->pageName, $hiddenInputs)}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}