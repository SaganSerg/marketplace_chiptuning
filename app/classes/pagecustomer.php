<?php
abstract class PageCustomer extends Page
{
    protected $lang;

    protected $dictionary;

    protected $dictionaryMain;

    protected $cookiesManagement;

    protected $dictionaryNavigation;

    protected $isCookiesAgree;

    protected $linksFooter = [
        'indexFooter' => '/',
        'aboutFooter' => '/about',
        'contactsFooter' => '/contacts'
    ];

    function __construct(string $name, $customer_id)
    {
        parent::__construct($name);
        $this->cookiesManagement = $GLOBALS['cookiesmanagement'];
        $this->dictionary = array_merge($this->composeDictionaryFooter(), $this->dictionaryMain, $this->composeDictionaryHeader(), $this->cookiesManagement->dictionaryWindowCookies, $this->dictionaryNavigation);
        $this->lang = (new LangManagement)->getLang($customer_id);
        $this->isCookiesAgree = $this->cookiesManagement->isAgree;
    }

    protected function getText($lang, $place) // $lang -- язык вида 'en'; $place -- это название массива являющегося элементом массива $date
    {
        if (isset($this->dictionary[$place][$lang])) {
            return $this->dictionary[$place][$lang];
        }
        // throw new Exception("Такой элемент массива не существует -- $place и $lang");
        return $place;
    }

    protected function getHiddenLangInput($value)
    {
        return $this->getHiddenInput($value, 'lang');
    }

    protected function getHiddenPageInput($value)
    {
        return $this->getHiddenInput($value, 'Page');
    }

    protected function getHiddenInputSet($lang, $pageName, $blockInputs = null)
    {
        return <<<HTML
            {$this->getHiddenLangInput($lang)}
            {$this->getHiddenPageInput($pageName)}
            $blockInputs
HTML;
    }

    protected function getNormalLink(string $link, string $class, string $text, string $pageName)
    {
        return <<<HTML
        <form action='$link' method="post" class='normal-link'>
            {$this->getHiddenInputSet($this->lang, $pageName)}
            <button class="normal-link__input $class" type="submit">$text</button>
        </form>
HTML;
    }
    protected function getNormalLinkNoLang(string $link, string $text, string $pageName, array $blockInputs = [], string $formClass = null,  string $submitClass = null)
    {
        return <<<HTML
        <form action='$link' method="post" class='normal-link-nolang $formClass'>
            {$this->getHiddenPageInput($pageName)}
            {$this->getHiddenInputBlock($blockInputs)}
            <input class="normal-link-nolang__input $submitClass" type="submit" value="$text">
        </form>
HTML;
    }
    protected function getNormalLinkWithAttrubutes(string $link, string $class, string $text, array $attributes) // формат массива $attributes [['name' => '1', 'value' => '1'], ['name' => '2', 'value' => '2']];
    {
        $html = "<form action='$link' method='post' class='normal-link'>\n" . $this->getHiddenPageInput($this->pageName);
        foreach ($attributes as $attribute) {
            $html .= $this->getHiddenInput($attribute['value'], $attribute['name']) . "\n";
        }
        return $html .= "<input class='normal-link__input $class' type='submit' value='$text'>\n</form>";

HTML;
    }
    protected function getSubmit(string $text, string $pageName, string $class = null, string $id = null)
    {
        return <<<HTML
        {$this->getHiddenInputSet($this->lang, $pageName)}
        <input class="$class" type="submit" value="$text" id="$id">
HTML;
    }

    protected function composeDictionaryHeader()
    {
        return [
            'langHeader' => [
                'en' => 'en',
                'ru' => 'ru'
            ],
            'noscriptHeader' => [
                'en' => 'You do not have JavaScript enabled. If you do not enable it, you will not be able to use our website.',
                'ru' => 'У Вас не включен JavaScript. Если Вы его не включите, Вы не сможете пользоваться нашим сайтом.'
            ], 
            'logoHeader' => [
                'en' => 'Briefly! Who are we!',
                'ru' => 'Кратко! Кто мы такие!'
            ], 
        ];
    }

    protected function composeDictionaryFooter()
    {
        return [
            'indexFooter' => [
                'en' => 'Home',
                'ru' => 'Главная'
            ], 
            'aboutFooter' => [
                'en'=> 'About',
                'ru' => 'О нас'
            ], 
            'contactsFooter' => [
                'en' => 'Contacts',
                'ru' => 'Контакты'
            ],
        ];
    }
    
    protected function header(string $classPage, string $classHeader, string $block)
    {
        return <<<HTML
        <!DOCTYPE html>
        <html class="document" lang="{$this->getText($this->lang, 'langHeader')}">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{$this->getText($this->lang, 'TitleMain')}</title>
            <link rel="stylesheet" href="/app/resources/styles/style.css">
        </head>
        <body class="document__page page $classPage">
            <noscript class="page__warning">{$this->getText($this->lang, 'noscriptHeader')}</noscript>
            <header class="page__header page__header_buttom-margin $classHeader">
                {$this->getNormalLink('/', "page__logo logo", $this->getText($this->lang, 'logoHeader'), $this->pageName)}
                <div class="page__lang-switcher lang-switcher">
                $block
                </div>
            </header>
            <main class="page__main main main_flex">
HTML;
    }

    protected function footer(string $windowCookie = null)
    {
        return <<<HTML
                </main>
            <footer class="page__footer footer">
                <nav class="footer_nav nav">
                    <ul class="nav__link-list">
                        {$this->getLinksFooter()}
                    </ul>
                </nav>
            </footer>
            $windowCookie
            <script src="/app/resources/scripts/script.js"></script>
        </body>
        </html>
HTML;  
    }
    
    protected function getLinksFooter()
    {
        $html = '';
        foreach($this->linksFooter as $mark => $link) {
            $html .= "<li class='nav__link-item'>{$this->getNormalLink($link, 'nav__link', $this->getText($this->lang, $mark), $this->pageName)}</li>";
        }
        return $html;
    }
    protected function getFormMessage(string $place, $condition, $class = '') // надо будет подставить тип второго аргумента bool
    {
        if ($condition) {
            return "<div class='form__message $class'>{$this->getText($this->lang, $place)}</div>";
        }
    }
}