<?php
class PageCustomerFacadeRegistration extends PageCustomerFacade
{
    protected $pageName = '/registration';

    protected const INPUT_ATTRIBUTE_NAME = [
        'registration' => [
            'tel' => 'Tel', 
            'email' => 'Email', 
            'valuta' => 'Valuta', 
            'pass' => 'Pass', 
            'repaetedPass' => 'RepeatedPass']
    ];

    public static $arrMessageNames = [
        'Tel' => [
            'Not11', 'NotOnly', 'Empty'
        ],
        'Email' => [
            'LongToo', 'Empty', 'NotFormat', 'Exist'
        ],
        'Pass' => [
            'Empty', 'ErrorLength', 'NotOnly'
        ],
        'RepeatedPass' => [
            'Empty', 'NotEqual'
        ],
        'Valuta' => [
            'Empty'
        ]
    ];
    protected $messageTelNot11;
    protected $messageTelNotOnly;
    protected $messageTelEmpty;

    protected $messageEmailNotFormat;
    protected $messageEmailLongToo;
    protected $messageEmailEmpty;
    protected $messageEmailExist;

    protected $massageValutaEmpty;

    protected $messagePassEmpty;
    protected $messagePassErrorLength;
    protected $messagePassNotOnly;

    protected $messageRepeatedPassNotEqual;

    protected $registrationEmail;

    public static $externalConditionEmailExist = false;

    function __construct(
        string $name,
        $customer_id, 
        $registrationEmail = null,
        array $showedMessageList = []
        )
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        parent::__construct($name, $customer_id);
        $this->assignValue($showedMessageList);
        $this->registrationEmail = (!$registrationEmail) ? $this->saveInputValue('Email') : $registrationEmail;
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
                case 'TelEmpty':
                case 'PassEmpty': 
                case 'RepeatedPassEmpty':
                case 'EmailEmpty':
                case 'ValutaEmpty':
                    return $length == 0;
                case 'EmailExist':
                    return self::$externalConditionEmailExist;
                case 'TelNot11':
                    return  $length >=1 && $length < 11 || $length > 11;
                case 'TelNotOnly':
                    return preg_match('/\D/', $input) === 1;
                case 'EmailNotFormat':
                    if ($length > 0) {
                        return preg_match('/^[a-zа-я0-9_\-\.]+@[a-zа-я0-9\-]+\.[a-zа-я0-9\-\.]+$/iu', $input) === 0;
                    }
                    return false;
                case 'EmailLongToo':
                    return $length > 50;
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
        if (isset($_POST['Page']) && !isset($_POST['Valuta'])) {
            return true;
        }
        return false;
    }
    private function getInputTextForComposeHTML($name, $placeholder) {
        return $this->getInput('text', $this->saveInputValue($name), $name, "form__input" , $placeholder);
    }
    private function getInputPassForComposeHTML($name) {
        return $this->getInput('password', null , $name, "form__input" , 'password');
    }
    private function getSelectForComposeHTML()
    {
        $valutas = [$GLOBALS["rub"] => 'Ruble', $GLOBALS["usd"] => 'Dollar', $GLOBALS["eur"] => 'Euro'];
        $select = "<select class='form__input' name='Valuta' size='1'>";
        
        if (!isset($_POST['Valuta']) || $_POST['Valuta'] === '') {
            $select .= "<option disabled selected value='empty'>{$this->getText($this->lang, 'ChooseValutaMain')}</option>";
        }
        foreach ($valutas as $valuta => $valutaMark) {
            $selected = '';
            if (isset($_POST['Valuta']) && $_POST['Valuta'] == $valuta) {
                $selected = 'selected';
            }
            $select .= "<option value='$valuta' $selected >{$this->getText($this->lang, $valutaMark . 'Main')}</option>";
        }
        return $select .= " </select>";
    }
    private function composeHTML() 
    {
        $registrationInput = self::INPUT_ATTRIBUTE_NAME['registration'];
        $passRegistrationInput = $registrationInput['pass'];
        $telRegistrationInput = $registrationInput['tel'];
        $emailRegistrationInput = $registrationInput['email'];
        $valutaRegistrationInput = $registrationInput['valuta'];
        $repaetedPassRegistrationInput = $registrationInput['repaetedPass'];
        $hiddenInputs = $this->getComposedHiddenInputs();
        return <<<HTML
        {$this->getFacadeHeader('registration', $hiddenInputs)}
        <form class="main__form form" method="POST" action="/pay">
            <fieldset class="form__block-form">
                <legend class="form__title">{$this->getText($this->lang, 'FormtitleMain')}</legend>
                <label class="form__wrapper-input">
                    {$this->getFormMessage('CookiesAgreeMain', $this->messageCookiesAgree)}
                    {$this->getFormMessage('EmailNotFormatMain', $this->messageEmailNotFormat)}
                    {$this->getFormMessage('EmailLongTooMain', $this->messageEmailLongToo)}
                    {$this->getFormMessage('EmailEmptyMain', $this->messageEmailEmpty)}
                    {$this->getFormMessage('EmailExistMain', $this->messageEmailExist)} 
                    <div class="form__description">Это адрес вашей электронной почты {$this->registrationEmail}</div>
                    <input type="hidden" value="{$this->registrationEmail}" name="$emailRegistrationInput"> <!--  надо будет заменить функцие для вывода скрытого -->
                    <span class="form__whatis">{$this->getText($this->lang, 'EmailMain')}</span>
                </label>
                <label class="form__wrapper-input">
                    {$this->getFormMessage('TelNot11Main', $this->messageTelNot11)}
                    {$this->getFormMessage('TelNotOnlyMain', $this->messageTelNotOnly)}
                    {$this->getFormMessage('TelEmptyMain', $this->messageTelEmpty)}
                    <div class="form__description">{$this->getText($this->lang, 'DiscriptionForTelMain')}</div>
                    {$this->getInputTextForComposeHTML($telRegistrationInput, $this->telExample)}
                    <span class="form__whatis">{$this->getText($this->lang, 'TelMain')}</span>
                </label>
                <label class="form__wrapper-input">
                    {$this->getFormMessage('ValutaEmptyMain', $this->messageValutaEmpty)}
                    <div class="form__description">{$this->getText($this->lang, 'DiscriptionForValutaMain')}</div>
                    {$this->getSelectForComposeHTML()}
                    <span class="form__whatis">{$this->getText($this->lang, 'ValutaMain')}</span>
                </label>
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