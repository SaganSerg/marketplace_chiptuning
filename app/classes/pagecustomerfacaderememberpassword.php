<?php
class PageCustomerFacadeRememberpassword extends PageCustomerFacade
{
    protected $pageName = '/rememberpassword';
    protected const INPUT_ATTRIBUTE_NAME = [
        'rememberspassword' => ['email' => "Email"]
    ];
    public static $arrMessageNames = [
        'Email' => [
            'LongToo', 'Empty', 'NotFormat'
        ]
    ];
    protected $messageEmailNotFormat;
    protected $messageEmailLongToo;
    protected $messageEmailEmpty;
    function __construct(
        string $name, 
        $customer_id,
        array $showedMessageList = []
        )
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        parent::__construct($name, $customer_id);
        $this->assignValue($showedMessageList);
    }
    static function is(string $inputName, string $whatCheck)
    {
        if (isset($_POST[$inputName])) {
            $input = $_POST[$inputName];
            $length = strlen($input);
            switch($whatCheck) {
                case 'EmailEmpty':
                    return $length == 0;
                case 'EmailNotFormat':
                    if ($length > 0) {
                        return preg_match('/^[a-zа-я0-9_\-\.]+@[a-zа-я0-9\-]+\.[a-zа-я0-9\-\.]+$/iu', $input) === 0;
                    }
                    return false;
                case 'EmailLongToo':
                    return $length > 50;
                    break;
                default: 
                    throw new Exception("Методу был передан неправильный аргумент whatCheck");
            }
        }
        if (isset($_POST['Page']) && !isset($_POST['Valuta'])) {
            return true;
        }
        return false;
    }
    private function composeDictionaryMain() 
    {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'For registered user' => [
                'en' => 'For registered user',
                'ru' => 'Для зарегистрированного пользователя'
            ], 
            'The email address is not in the correct format' => [
                'en' => 'The email address is not in the correct format',
                'ru' => 'Адрес электронной почты введен в неправильном формате'
            ],
            'Email address is too long' => [
                'en' => 'Email address is too long',
                'ru' => 'Адрес электронной почты слишком длинный'
            ],
            'You have not entered anything in the e-mail field' => [
                'en' => 'You have not entered anything in the e-mail field',
                'ru' => 'Вы ничего не ввели в поле эл.почта'
            ],
            'E-mail' => [
                'en' => 'E-mail',
                'ru' => 'Эл.почта'
            ],
            'Enter the email address to which your account was registered' => [
                'en' => 'Enter the email address to which your account was registered',
                'ru' => 'Введите адрес электронной почты, на которую был зарегистрирован Ваш аккаунт'
            ],
            'Receive an email' => [
                'en' => 'Receive an email ',
                'ru' => 'Получить письмо на электронную почту'
            ]

        ];
    }
    private function getInputTextForComposeHTML($name, $placeholder) {
        return $this->getInput('text', $this->saveInputValue($name), $name, "form__input" , $placeholder);
    }
    private function composeHTML() 
    {
        $rememeberpasswordInput = self::INPUT_ATTRIBUTE_NAME['rememberspassword'];
        $hiddenInputs = $this->getComposedHiddenInputs();
        $emailRegistrationInput = $rememeberpasswordInput['email'];
        return <<<HTML
        {$this->getFacadeHeader('index', $hiddenInputs)}
        <form class="main__form form" method="POST" action="/sentmail">
        {$this->getHiddenPageInput($this->pageName)}
        {$this->getHiddenInput($this->saveInputValue('lang'), 'lang')}
            <fieldset class="form__block-form">
                <legend class="form__title">{$this->getText($this->lang, 'For registered user')}</legend>
                <label class="form__wrapper-input">
                    {$this->getFormMessage('The email address is not in the correct format', $this->messageEmailNotFormat)}
                    {$this->getFormMessage('Email address is too long', $this->messageEmailLongToo)}
                    {$this->getFormMessage('You have not entered anything in the e-mail field', $this->messageEmailEmpty)}
                    <div class="form__description">{$this->getText($this->lang, 'Enter the email address to which your account was registered')}</div>
                    {$this->getInput('text', $this->saveInputValue($emailRegistrationInput), $emailRegistrationInput, "form__input" , $this->getText($this->lang, "E-mail"))}
                </label>
                <input class="form__button button button__transparent" type="submit" value="{$this->getText($this->lang, 'Receive an email')}">
            </fieldset>
        </form>
        {$this->getFacadeFooter($this->pageName, $hiddenInputs)}
    
HTML;
    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}