<?php
abstract class PageCustomerFacade extends PageCustomer
{
    protected $messageCookiesAgree; // messageCookiesAgree  messageAgreeCookies

    protected const ARR_FOR_LANG = [
        ['ru', 'rus', 'РУС'], 
        ['en', 'eng', 'ENG']
    ];

    protected function getComposedHiddenInputs()
    {
        $arr = static::INPUT_ATTRIBUTE_NAME;
        $hiddenInputs = '';
        foreach ($arr as $arrPlace) {
            foreach ($arrPlace as $name) {
                $hiddenInputs .= $this->getHiddenInput($this->saveInputValue($name), $name);
            }
        }
        return $hiddenInputs;
    }

    protected function checkArrElemExist(array $arr, string $elemName)
    {
        return (isset($arr[$elemName]) && $arr[$elemName]);
    }

    function __construct($name, $customer_id)
    {
        $this->dictionaryNavigation = [];
        parent::__construct($name, $customer_id);
    }

    private function getElementLangSwitcher(string $lang, string $classPrefix, string $value, string $blockInputs = null) : string
    {
        return <<<HTML
            <form class="lang-switcher__wrapper-input" action="{$this->getRequerMyself()}" method="POST">
                {$this->getHiddenInputSet($lang, $this->pageName, $blockInputs)}
                <input class="lang-switcher__item lang-switcher__item_$classPrefix" type="submit" value="$value">
            </form>
HTML;
    }

    protected function getBlockLangSwitcher(array $arr, string $blockInputs = null) : string 
    {
        $html = '';
        foreach ($arr as $data) {
            $html .= $this->getElementLangSwitcher($data[0], $data[1], $data[2], $blockInputs);
        }
        return $html;
    }
    // abstract static function is(string $inputName, string $whatCheck);

    static function isMessageCookiesAgree()
    {
        return isset($_POST['Page']) && !$GLOBALS['cookiesmanagement']->isAgree;;
    }

    

    protected function getCookieWindow(string $submit, string $title, string $explanation, string $url, string $lang, string $inputLang)
    {
        return $this->cookiesManagement->returnHTML(
            $submit, 
            $title, 
            $explanation, 
            $url, 
            $lang, 
            $inputLang,
            $this->getNormalLink('/termsuse', '', $this->getText($this->lang, 'cookieTerms'), $this->pageName)
        );
    }

    protected function getFacadeFooter($pageName, $otherInput)
    {
        return $this->footer($this->getCookieWindow($this->getText($this->lang, 'agreeWindowCookie'), $this->getText($this->lang, 'agreementtitleWindowCookie'), $this->getText($this->lang, 'agreementexplanationWindowCookie'), $this->getRequerMyself(), $this->lang, $this->getHiddenInputSet($this->lang, $pageName, $otherInput)));
        
    }

    protected function getFacadeHeader($page, string $blockInputs = null)
    {
        return $this->header("page_$page", 'page__header_light', $this->getBlockLangSwitcher(self::ARR_FOR_LANG, $blockInputs));
    }
    static function getInputNameList()
    {
        $arrMessageNames = static::$arrMessageNames;
        $arrMessages = [];
        foreach ($arrMessageNames as $inputName => $messagePrefixes ) {
            foreach ($messagePrefixes as $prefix) {
                $elemName = $inputName . $prefix;
                $arrMessages[$elemName] = static::is($inputName, $elemName);
            }
        }
        $arrMessages['CookiesAgree'] = static::isMessageCookiesAgree();
        return $arrMessages;
    }
    function assignValue(array $showedMessageList)
    {
        if (!$showedMessageList) {
            self::getInputNameList();
        }
        else {
            foreach ($showedMessageList as $elemName => $elemValue) {
                $propertyName = 'message' . $elemName;
                $this->$propertyName = $elemValue;
            }
        }
        
    }
}