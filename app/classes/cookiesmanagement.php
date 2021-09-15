<?php
class CookiesManagement 
{
    private function __construct(){}
    private function __clone(){}
    private static $instance;
    private $formMethod;
    private $cookieName;
    private $cookieValue;
    private $storageDaysCookie;
    public $isAgree;
    public $dictionaryWindowCookies = [
        'agreementtitleWindowCookie' => [
            'en' => 'We use cookies',
            'ru' => 'Мы используем файлы cookie'
        ], 
        'agreementexplanationWindowCookie' => [
            'en' => 'By continuing to browse the site, you agree to ',
            'ru' => 'Продолжая использовать сайт, вы соглашаетесь с '
        ],
        'agreeWindowCookie' => [
            'en' => 'I agree',
            'ru' => 'Согласен'
        ],
        'cookieTerms' => [
            'en' => 'the cookie terms',
            'ru' => 'условиями использования файлов куки'
        ]
    ];
    function getIsAgree()
    {
        return $this->isAgree;
    }
    function customerSetCookie(string $name, string $value = "", array $options) : bool
    {
        if ($this->isAgree) {
            return setcookie($name, $value, $options);
        }
        return false;
    }
    function setAgreeToTrue()
    {
        $this->isAgree = true;
    }
    function setAgreeToFalse()
    {
        $this->isAgree = false;
    }
    function customerSessionStart() : bool
    {
        if ($this->isAgree) {
            return session_start();
        }
        return false;
    }
    function setAgree()
    {
        if ((isset($_POST[$this->cookieName]) && $_POST[$this->cookieName] == $this->cookieValue)) {
            $this->setAgreeToTrue();
            $time = (int) time()+60*60*24*$this->storageDaysCookie;
            $this->customerSetCookie($this->cookieName, $this->cookieValue, ['expires' => $time]);
        }
    }
    function returnHTML(string $submit, string $title, string $explanation, string $url, string $lang, string $inputLang, string $linkPage) {
        if(!$this->isAgree) {
            return <<<HTML
            <div class='page__agreement agreement'>
                <div class='agreement__title'>$title</div>\n\t
                <div class='agreement__explanation'>$explanation $linkPage</div>\n\t
                <form class='agreement__agreement-form' action='$url' method='{$this->formMethod}'>\n\t
                    <input type='hidden' name='{$this->cookieName}' value='{$this->cookieValue}'>\n\t
                    $inputLang\n\t
                    <input class='agreement__agreement-submit' type='submit' value='$submit' >\n
                </form>
            </div>
HTML;
        }
    }
    static function getInstance(
        $formMethod = 'POST',
        $cookieName = 'agreement',
        $cookieValue = 'yes',
        $storageDaysCookie = 90
    )
    {
        if (self::$instance == null) {
            self::$instance = new self;
            self::$instance->formMethod = $formMethod;
            self::$instance->cookieName = $cookieName;
            self::$instance->cookieValue = $cookieValue;
            self::$instance->storageDaysCookie = $storageDaysCookie;
            self::$instance->setAgreeToFalse();
            if ((isset($_COOKIE[self::$instance->cookieName]) && $_COOKIE[self::$instance->cookieName] == self::$instance->cookieValue)) {
                self::$instance->setAgreeToTrue();
            }
        }
        return self::$instance;
    }
}