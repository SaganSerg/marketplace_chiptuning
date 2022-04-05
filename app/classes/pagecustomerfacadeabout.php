<?php
class PageCustomerFacadeAbout extends PageCustomerFacade
{
    protected $pageName = '/about';
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
            'PageAbout' => [
                'en' => 'this page is about us',
                'ru' => 'Эта страничка о нас'
            ]
        ];
    }
    
    private function composeHTML() 
    {
        $hiddenInputs = $this->getComposedHiddenInputs();
        return <<<HTML
        {$this->getFacadeHeader('about', $hiddenInputs)}
        <article class='main__facade-article facade-article'>
            <h1 class='facade-article__title'>{$this->getText($this->lang, 'PageAbout')}</h1>
            <p class='facade-article__paragraph'>Это первый абзац о нас</p>
            <p class='facade-article__paragraph'>Это второй абзац о нас</p>
        </article>
       
        {$this->getFacadeFooter($this->pageName, $hiddenInputs)}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}