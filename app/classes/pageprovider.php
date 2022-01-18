<?php
abstract class PageProvider extends Page
{
    protected $showLink = true;
    function __construct(string $name)
    {
        parent::__construct($name);
    }
    protected function normalLink(string $classForm, string $classButton, string $action, string $caption, array $parameters = [])
    {
        $html = '';
        foreach ($parameters as $parameterName => $parameterValue) {
            $html .= "<input type='hidden' name='$parameterName' value='$parameterValue'>\n"; 
        }
        return <<<HTML
        <form class='normal-link-provider $classForm' method='POST' action='$action'>
            $html
            <button class='normal-link-provider__button $classButton' type='submit'>$caption</button>
    </form>
HTML;
    }
    protected function dateFromTimestampDMY ($timestamp)
    {
        return date("d.m.Y", $timestamp);
    }
    protected function dateFromTimestampDMYHMS ($timestamp)
    {
        return date("d.m.Y H:i:s", $timestamp);
    }
    private function headerLink()
    {
        $linksArr = [
            'Admin' => [
                'action' => '/admin',
                'pageName' => '/admin',
                'caption' => 'Админка'
            ],
            'Deals' => [
                'action' => '/deals',
                'pageName' => '/deals',
                'caption' => 'Сделки'
            ]
        ];
        $html = '';
        if ($this->showLink) {
            $html .= '<ul class="provider-main-menu">';
            foreach ($linksArr as $elem) {
                $activeButton = $this->pageName == $elem['pageName'] ? 'provider-normal-link__button_not-active' : '';
                $html .= "<li class='provider-main-menu__element'>{$this->normalLink('provider-normal-link', 'provider-normal-link__button '. $activeButton, $elem['action'], $elem['caption'], ['Page' => $this->pageName])}</li>";
            }
            $html .= '</ul>';
        }
        return $html;
    }
    protected function header()
    {
        return <<<HTML
        <!DOCTYPE html>
        <html class="document" lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Рабочая область</title>
            <link rel="stylesheet" href="/app/resources/styles/style.css">
        </head>
        <body class="">
            <header class="">
                {$this->headerLink()}
            </header>
            <main class="">
HTML;
    }
    protected function getHeader()
    {
        return $this->header();
    }
    protected function getFooter()
    {
        return $this->footer();
    }

    protected function footer()
    {
        return <<<HTML
                </main>
            <footer class="">
                
            </footer>
            <script src="/app/resources/scripts/script.js"></script>
        </body>
        </html>
HTML;  
    }
}