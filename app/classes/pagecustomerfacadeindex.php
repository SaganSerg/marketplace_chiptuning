<?php
class PageCustomerFacadeIndex extends PageCustomerFacade
{
    protected $pageName = '/index';
    protected const INPUT_ATTRIBUTE_NAME = [
        'registration' => ['email' => "Email", 'pass' => 'Pass']
    ];
    
    public static $arrMessageNames = [
        'Email' => [
            'Empty', 'NotExist'
        ],
        'Pass' => [
            'Empty', 'Wrong'
        ]
    ];
    
    private $messageEmailPassWrong;
    protected $messagePassWrong; // это промежуточное свойство, такого послания нет
    protected $messageEmailNotExist; // это промежуточное свойство, такого послания нет
    protected $messageEmailEmpty;
    protected $messagePassEmpty;
    public static $externalConditionEmailNotExist;
    public static $externalConditionPassWrong;
    function __construct(
        string $name, $customer_id, array $arr = []
        )
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        parent::__construct($name, $customer_id);
        $this->assignValue($arr);
        // $this->messageLoginExist = $this->checkArrElemExist($arr, 'LoginExist');
        // $this->messagePassWrong = $this->checkArrElemExist($arr, 'PassWrong');
        // $this->messageLoginEmpty = $this->checkArrElemExist($arr, 'LoginEmpty');
        
        // $this->messagePassEmpty = $this->checkArrElemExist($arr, 'PassEmpty');
        // $this->messageCookiesAgree = $this->checkArrElemExist($arr, 'AgreeCookies');
        $this->messageEmailPassWrong = $this->messagePassWrong || $this->messageEmailNotExist;
        
    }
    
    private function composeDictionaryMain() 
    {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'FormTitleMain' => [
                'en' => 'For registered user',
                'ru' => 'Для зарегистрированного пользователя'
            ], 
            'CookiesAgreeMain' => [
                'en' => 'You must give your consent to the use of cookies',
                'ru' => 'Вы должны дать свое согласие на использование файлов-cookie'
            ],
            'WrongEmailPassMain' => [
                'en' => 'Either wrong login or wrong password',
                'ru' => 'Либо неправильная эл.почта, либо неправильный пароль'
            ], 
            'NoEmailMain' => [
                'en' => 'You have not entered anything in the login field',
                'ru' => 'Вы ничего не ввели в поле эл.почта'
            ], 
            'NoPassMain' => [
                'en' => 'You did not enter anything in the password input field',
                'ru' => 'Вы ничего не ввели в поле ввода пароля'
            ], 
            'ComeInMain' => [
                'en' => 'Come in',
                'ru' => 'Войти'
            ],
            'RegistrationMain' => [
                'en' => 'Register now',
                'ru' => 'Зарегистрироваться'
            ],
            'EmailMain' => [
                'en' => 'E-mail',
                'ru' => 'Эл.почта'
            ],
            'PasswordMain' => [
                'en' => 'Password',
                'ru' => 'Пароль'
            ],
            'RememberpasswordMain' => [
                'en' => 'Remember password',
                'ru' => 'Вспомнить пароль'
            ]
        ];
    }
    static function is(string $inputName, string $whatCheck)
    {
        if (isset($_POST[$inputName])) {
            $input = $_POST[$inputName];
            $length = strlen($input);
            switch($whatCheck) {
                case 'EmailEmpty':
                case 'PassEmpty': 
                    return $length == 0;
                case 'EmailNotExist':
                    if ($length > 0) {
                        return self::$externalConditionEmailNotExist;
                    }
                    return false;
                    
                case 'PassWrong':
                    if ($length > 0) {
                        return self::$externalConditionPassWrong;
                    }
                    return false;
                    break;
                default: 
                    throw new Exception("Методу был передан неправильный аргумент whatCheck");
            }
        }
        return false;
    }
    private function composeHTML() 
    {
        $registrationInput = self::INPUT_ATTRIBUTE_NAME['registration'];
        $emailRegistrationInput = $registrationInput['email'];
        $passRegistrationInput = $registrationInput['pass'];
        $hiddenInputs = $this->getComposedHiddenInputs();
        return <<<HTML
        {$this->getFacadeHeader('index', $hiddenInputs)}
        <form class="main__form form" method="POST" action="/pay">
        {$this->getHiddenPageInput($this->pageName)}
        {$this->getHiddenInput($this->saveInputValue('lang'), 'lang')}
            <fieldset class="form__block-form">
                <legend class="form__title">{$this->getText($this->lang, 'FormTitleMain')}</legend>
                {$this->getFormMessage('CookiesAgreeMain', $this->messageCookiesAgree)}
                {$this->getFormMessage('WrongEmailPassMain', $this->messageEmailPassWrong)}
                <label class="form__wrapper-input">
                    {$this->getFormMessage('NoEmailMain', $this->messageEmailEmpty)}
                    {$this->getInput('text', $this->saveInputValue($emailRegistrationInput), $emailRegistrationInput, "form__input" , $this->getText($this->lang, "EmailMain"))}
                </label>
                <label class="form__wrapper-input">
                    {$this->getFormMessage('NoPassMain', $this->messagePassEmpty)}
                    {$this->getInput('password', null, $passRegistrationInput, "form__input" , $this->getText($this->lang, "PasswordMain"))}
                </label>
                <input class="form__button button button__transparent" type="submit" value="{$this->getText($this->lang, 'ComeInMain')}">
            </fieldset>
        </form>
        {$this->getNormalLink('/registration', "main__button button button__green", $this->getText($this->lang, "RegistrationMain"), 'Registration')}
        {$this->getNormalLink('/rememberpassword', "main__button button button__green", $this->getText($this->lang, "RememberpasswordMain"), $this->pageName)}
        {$this->getFacadeFooter($this->pageName, $hiddenInputs)}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}