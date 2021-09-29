<?php
class PageCustomerFacadeNewpassword extends PageCustomerFacade
{
    protected $pageName = '/newpassword';
    protected $customer_email;

    protected const INPUT_ATTRIBUTE_NAME = [
        'registration' => [
            'pass' => 'Pass', 
            'repaetedPass' => 'RepeatedPass']
    ];

    public static $arrMessageNames = [
        'Pass' => [
            'Empty', 'ErrorLength', 'NotOnly'
        ],
        'RepeatedPass' => [
            'NotEqual'
        ]
    ];
    

    

   

    protected $messagePassEmpty;
    protected $messagePassErrorLength;
    protected $messagePassNotOnly;

    protected $messageRepeatedPassNotEqual;

    function __construct(
        string $name,
        $customer_id, 
        $customer_email = null,
        array $showedMessageList = []
        )
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        parent::__construct($name, $customer_id);
        $this->assignValue($showedMessageList);
        $this->customer_email = $customer_email;
    }

    private function composeDictionaryMain()
    {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'FormtitleMain' => [
                'en' => 'Initial registration',
                'ru' => 'Первичная регистрация'
            ],
            'LoginEmptyMain' => [
                'en' => 'You have not entered anything in the login field',
                'ru' => 'Вы ничего не ввели в поле логина'
            ],
            'CookiesAgreeMain' => [
                'en' => 'You must give your consent to the use of cookies',
                'ru' => 'Вы должны дать свое согласие на использование файлов-cookie'
            ],
            'LoginLess3Main' => [
                'en' => 'Your login is less than 3 characters',
                'ru' => 'Вaш логин меньше 3 знаков'
            ],
            'LoginMore10Main' => [
                'en' => 'Your login is more than 10 characters',
                'ru' => 'Вaш логин больше 10 знаков'
            ],
            'LoginNotOnlyMain' => [
                'en' => 'Your login consists not only of numbers and Latin characters',
                'ru' => 'Вaш логин cocтоит не только из цифр и латинских знаков'
            ],
            'LoginExistMain' => [
                'en' => 'This login is already registered in the database',
                'ru' => 'Такой логин уже зарегистрирован в базе'
            ],
            'DiscriptionForLoginMain' => [
                'en' => 'Create a username. It should only consist of Latin letters and numbers. Login length must be at least 3 and no more than 10 characters.',
                'ru' => 'Придумайте логин. Он должен состоять только из латинских букв и цифр. Длина логина должна быть не меньше 3-х и не больше 10-и знаков.'
            ],
            'EmailNotFormatMain' => [
                'en' => 'The entered data is not a valid email format',
                'ru' => 'Введенные данные не являются допустимым форматом электронной почты'
            ],
            'EmailLongTooMain' => [
                'en' => 'Your email address must be no more than 50 characters',
                'ru' => 'Длина вашего электронного адреса должна быть не больше 50 знаков'
            ],
            'EmailEmptyMain' => [
                'en' => 'You have not entered anything in the email address field',
                'ru' => 'Вы ничего не ввели в поле адреса электронной почты'
            ],
            'EmailExistMain' => [
                'en' => 'This email address is already registered',
                'ru' => 'Такой электронный адрес уже зарегистрирован'
            ],
            'TelNot11Main' => [
                'en' => 'The phone number does not contain 11 characters',
                'ru' => 'В телефонном номере указано не 11 знаков'
            ],
            'TelNotOnlyMain' => [
                'en' => 'The phone number contains more than numbers',
                'ru' => 'В телефонном номере указаны не только цифры'
            ],
            'TelEmptyMain' => [
                'en' => 'You did not enter anything in the phone number field',
                'ru' => 'Вы ничего не ввели в поле номера телефона'
            ],
            'PassErrorLengthMain' => [
                'en' => 'Password too long or too short',
                'ru' => 'Слишком большая или слишком маленькая длина пароля'
            ],
            'PassNotOnlyMain' => [
                'en' => 'Your password consists not only of numbers and Latin characters',
                'ru' => 'Вaш пароль cocтоит не только из цифр и латинских знаков'
            ],
            'PassEmptyMain' => [
                'en' => 'You did not enter anything in the password input field',
                'ru' => 'Вы ничего не ввели в поле ввода пароля'
            ],
            'RepeatedPassNotEqual' => [
                'en' => 'Password mismatch',
                'ru' => 'Пароли не совпадают'
            ],
            'LoginMain' => [
                'en' => 'Login',
                'ru' => 'Логин'
            ],
            'DiscriptionForTelMain' => [
                'en' => 'Enter the phone number according to the scheme' . $this->telExample,
                'ru' => 'Введите номер телефона по схеме ' . $this->telExample
            ],
            'TelMain' => [
                'en' => 'Phone number',
                'ru' => 'Номер телефона'
            ],
            'DiscriptionForEmailMain' => [ 
                'en' => 'Please enter your email address. And the address must have an @ sign! We assume that your electronic signature is no longer than 50 characters.',
                'ru' => 'Введите адрес электронной почты. А адресе обязательно должен присутсвовать знак @! Мы предполагаем что Ваш электронная подпись не длиннее 50 знаков.'
            ],
            'EmailMain' => [
                'en' => 'E-mail address',
                'ru' => 'Адрес электронной почты'
            ],
            'DiscriptionForValutaMain' => [
                'en' => 'Choose the currency you will use for calculations',
                'ru' => 'Выберете валюту которую будете использовать для расчетов'
            ],
            'ChooseValutaMain' => [
                'en' => 'Choose currency',
                'ru' => 'Выберите валюту'
            ],
            'RubleMain' => [
                'en' => 'Ruble',
                'ru' => 'Рубль'
            ],
            'DollarMain' => [
                'en' => 'Dollar',
                'ru' => 'Доллар'
            ],
            'EuroMain' => [
                'en' => 'Euro',
                'ru' => 'Евро'
            ],
            'ValutaMain' => [
                'en' => 'Currency',
                'ru' => 'Валюта'
            ],
            'DiscriptionForPassMain' => [
                'en' => 'Enter a password consisting of Latin letters and numbers. The length is no less than 5 and no more than 10 characters.',
                'ru' => 'Введите пароль состоящий из латинских букв и цифр. Длинной не меньше 5 и не больше 10 знаков.'
            ],
            'PassMain' => [
                'en' => 'Password',
                'ru' => 'Пароль'
            ],
            'DiscriptionForRepeatedPassMain' => [
                'en' => 'Repeat the password entered in the previous field.',
                'ru' => 'Повторите пароль введенный в предыдущем поле.'
            ],
            'PasswordConfirmationMain' => [
                'en' => 'Password confirmation',
                'ru' => 'Подтверждение пароля'
            ],
            'ValutaEmptyMain' => [
                'en' => 'You have not selected a currency',
                'ru' => 'Вы не выбрали валюту'
            ],
            'SendMain' => [
                'en' => 'Send',
                'ru' => 'Отправить'
            ]
        ];
    }

    static function is(string $inputName, string $whatCheck)
    {
        if (isset($_POST[$inputName])) {
            $input = $_POST[$inputName];
            $length = strlen($input);
            switch($whatCheck) {
               
                case 'PassEmpty':
                    return $length == 0;
                case 'PassErrorLength':
                        return $length >= 1 && $length < 5 || $length > 10;     
                case 'PassNotOnly':
                        return preg_match('/\W/', $input) === 1;
                case 'RepeatedPassNotEqual':
                        $inputPass = isset($_POST['Pass']) ? $_POST['Pass'] : '';
                        return $inputPass !== $input;
                    break;
                default: 
                    throw new Exception("Методу был передан неправильный аргумент whatCheck");
            }
        }
        return false;
    }
    
    private function getInputPassForComposeHTML($name) {
        return $this->getInput('password', null , $name, "form__input" , 'password');
    }
    
    private function composeHTML() 
    {
        $registrationInput = self::INPUT_ATTRIBUTE_NAME['registration'];
        $passRegistrationInput = $registrationInput['pass'];
        
        $repaetedPassRegistrationInput = $registrationInput['repaetedPass'];
        $hiddenInputs = $this->getComposedHiddenInputs();
        return <<<HTML
        {$this->getFacadeHeader('registration', $hiddenInputs)}
        <form class="main__form form" method="POST" action="{$this->name}">
            <fieldset class="form__block-form">
                <legend class="form__title">{$this->getText($this->lang, 'FormtitleMain')}</legend>
                <label class="form__wrapper-input">
                    {$this->getFormMessage('PassErrorLengthMain', $this->messagePassErrorLength)}
                    {$this->getFormMessage('PassNotOnlyMain', $this->messagePassNotOnly)}
                    {$this->getFormMessage('PassEmptyMain', $this->messagePassEmpty)} 
                    <div class="form__description">{$this->getText($this->lang, 'DiscriptionForPassMain')}</div>
                    {$this->getInputPassForComposeHTML("Pass")}
                    <span class="form__whatis">{$this->getText($this->lang, 'PassMain')}</span>
                </label>
                <label class="form__wrapper-input">
                    {$this->getFormMessage('RepeatedPassNotEqual', $this->messageRepeatedPassNotEqual)}
                    <div class="form__description">{$this->getText($this->lang, 'DiscriptionForRepeatedPassMain')}</div>
                    {$this->getInputPassForComposeHTML("RepeatedPass")}
                    <span class="form__whatis">{$this->getText($this->lang, 'PasswordConfirmationMain')}</span>
                </label>
                {$this->getSubmit($this->getText($this->lang, 'SendMain'), $this->pageName, "form__button button button__transparent")}
            </fieldset>
        </form>
        {$this->getFacadeFooter($this->pageName, $hiddenInputs)}
HTML;
    }

    function getHTML()
    {
        return $this->composeHTML();
    }

    private $telExample = '71001234567';
    
    
    

    
    // static function parseMessageNames()
    // {
    //     $arrMessages = [];
    //     foreach (self::$arrMessageNames as $inputName => $messagePrefixes ) {
    //         foreach ($messagePrefixes as $prefix) {
    //             $elemName = $inputName . $prefix;
    //             $arrMessages[$elemName] = self::is($inputName, $elemName);
    //         }
    //     }
    //     $arrMessages['AngreeCookies'] = self::isMessageCookiesAgree();
    //     return $arrMessages;
    // } 
    
    
    // function assignValue(array $showedMessageList)
    // {
    //     if (!$showedMessageList) {
    //         self::getInputNameList();
    //     }
    //     else {
    //         foreach ($showedMessageList as $elemName => $elemValue) {
    //             $propertyName = 'message' . $elemName;
                
    //             $this->$propertyName = $elemValue;
    //         }
    //     }
        
    // }
    


    
}