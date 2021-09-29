<?php
class PageCustomerCabinetBigfile extends PageCustomerCabinet
{
    protected $pageName = '/bigfile';
    function __construct($name, $customer_id, $login, $coins)
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        parent::__construct($name, $customer_id, $login, $coins);
    }
    private function composeDictionaryMain() {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'VeryBigFile' => [
                'en' => 'File too large',
                'ru' => 'Слишком большой файл'
            ],
            'Send' => [
                'en' => 'Send',
                'ru' => 'Отправить'
            ]
        ];
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getFacadeHeader()}
        {$this->getNavigation()}
        
        <article class="main__content content content_big_file">
            <h1 class="content__title">{$this->getText($this->lang, 'VeryBigFile')}</h1>
            <form method="POST" enctype="multipart/form-data" action='/treatment'>
                <input type="hidden" name="MAX_FILE_SIZE" value="{$GLOBALS['fileSizeFromCustomer']}">
                <input id="vehicle-original-file" type="file" name="original_file">
                <input id="checksum" type="hidden" name="checksum">
                {$this->getSubmit($this->getText($this->lang, 'Send'), $this->pageName, 'button button_form hide', 'submit-file')}
            </form>
        </article>
        {$this->getFacadeFooter()}
HTML;
    }
    function getHTML()
    {
        return $this->composeHTML();
    }
}