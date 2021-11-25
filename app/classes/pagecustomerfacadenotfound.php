<?php
class PageCustomerFacadeNotfound extends PageCustomerFacade
{
    protected $pageName = 'Notfound';
    protected const INPUT_ATTRIBUTE_NAME = [];
    function __construct(
        string $name, $customer_id, array $arr = []
        )
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        parent::__construct($name, $customer_id);
    }
    // function getHTML()
    // {
    //     return "Something went wrong!";
    // }
    private function composeDictionaryMain() 
    {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'WentWrong' => [
                'en' => 'Something went wrong!',
                'ru' => 'Что-то пошло не так!'
            ], 
            'CookiesAgreeMain' => [
                'en' => 'You must give your consent to the use of cookies',
                'ru' => 'Вы должны дать свое согласие на использование файлов-cookie'
            ]
        ];
    }
    private function composeHTML() 
    {
        $hiddenInputs = $this->getComposedHiddenInputs();
        return <<<HTML
        {$this->getFacadeHeader('notfound', $hiddenInputs)}
        <article class='main__facade-article facade-article'>
            <h1 class='facade-article__title'>{$this->getText($this->lang, 'WentWrong')}</h1>
        </article>
        {$this->getFacadeFooter($this->pageName, $hiddenInputs)}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}