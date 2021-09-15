<?php
class LangManagement
{
    private $langDefoult; 
    private $langMatching; /* это массив сопоставления языков, для каких мы показываем русский, а для каких другой язык. 
    Пример langMatching 
        $langs = [
            'ru'=>['ru','be','uk','ky','ab','mo','et','lv'],
            'tr'=>['tr']
        ];
    */
    private $name; // это имя которое будет использоваться как имя input в форме, а так же как имя cookie
    private $lang;
    function __construct(string $langDefoult = 'en', array $langMatching = ['ru'=>['ru','be','uk','ky','ab','mo','et','lv']], string $name = 'lang') {
        $this->langDefoult = $langDefoult;
        $this->langMatching = $langMatching;
        $this->name = $name;
    }
    private function getHeaderLang() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) 
            AND 
            $http = $_SERVER['HTTP_ACCEPT_LANGUAGE'] 
            AND 
            preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', strtolower($http), $list)) {
            $langs = array_combine($list[1], $list[2]);
            foreach ($langs as $n => $v) { 
                $langs[$n] = $v ? $v : 1; 
            }
            return asort($langs) ? substr(trim(array_key_last($langs)), 0, 2) : null;
        }
    }
    private function getPostLang() {
        if (isset($_POST[$this->name])) return $_POST[$this->name];
    }
    private function getGetLang()
    {
        if (isset($_GET[$this->name])) return $_GET[$this->name];
    }
    private function getCookieslang() {
        if (isset($_COOKIE[$this->name])) return $_COOKIE[$this->name];
    }
    private function getDBLang($customer_id) {
        if ($customer_id) {
            $model = new Model();
            $appDB = $model->appDB();
            return $model->getCustomerElementById($appDB, 'customer_language', $customer_id);
        }
    }
    function getLang($customer_id)
    {
        if ($lang = $this->getPostLang()) return $lang;
        // if ($lang = $this->getGetLang()) return $lang;
        if ($lang = $this->getDBlang($customer_id)) return $lang;
        if ($lang = $this->getCookieslang()) return $lang;
        if ($lang = $this->getHeaderLang()) return $lang;
        return $this->langDefoult;
    }
}