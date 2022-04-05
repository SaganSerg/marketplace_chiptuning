<?php
class PageCustomerCabinetProfile extends PageCustomerCabinet
{
    protected $pageName = '/profile';
    protected const INPUT_ATTRIBUTE_NAME = [
        'reregistration' => [
            'tel' => 'Tel',
            'lang' => 'Lang',
            'valuta' => 'Valuta'
            ]
    ];
    public static $arrMessageNames = [
        'Tel' => [
            'Not11', 'NotOnly', 'Empty'
        ],
        'Valuta' => [
            'Empty'
        ]
    ];
    protected $profile;
    

    protected $messageTelNot11;
    protected $messageTelNotOnly;
    protected $messageTelEmpty;

    

    protected $massageValutaEmpty;
    function __construct(
        string $name,
        $customer_id, 
        string $login,
        int $coins,
        array $profile,
        array $showedMessageList = []
        )
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        parent::__construct($name, $customer_id, $login, $coins);
        $this->profile = $profile;
        $this->assignValue($showedMessageList); // надо будет данный метод перенести наверное в трейт, сейчас он объявляется в pagecustomerfacade
    }
    private function composeDictionaryMain()
    {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'FormtitleMain' => [
                'en' => 'Here you can change your details',
                'ru' => 'Здесь вы можете изменить ваши данные'
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
            ],
            'rusMain' => [
                'en' => 'Russian language',
                'ru' => 'Русский язык'
            ],
            'engMain' => [
                'en' => 'English language',
                'ru' => 'Английский язык'
            ],
            'ChooseLangMain' => [
                'en' => 'Choose language',
                'ru' => 'Выберите язык'
            ],
            'LanguageMain' => [
                'en' => 'Language',
                'ru' => 'Язык'
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
                case 'ValutaEmpty':
                    return $length == 0;
                case 'TelNot11':
                    return  $length >=1 && $length < 11 || $length > 11;
                case 'TelNotOnly':
                    return preg_match('/\D/', $input) === 1;
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
    function assignValue(array $showedMessageList) // данный метод надо будет удалить, а потом разместить в трейте
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
    static function getInputNameList() // этот метод переделан
    {
        $arrMessageNames = static::$arrMessageNames;
        $arrMessages = [];
        foreach ($arrMessageNames as $inputName => $messagePrefixes ) {
            foreach ($messagePrefixes as $prefix) {
                $elemName = $inputName . $prefix;
                $arrMessages[$elemName] = static::is($inputName, $elemName);
            }
        }
        // $arrMessages['CookiesAgree'] = static::isMessageCookiesAgree();
        return $arrMessages;
    }
    static function isMessageCookiesAgree() // данный метод надо будет удалить, а потом разместить в трейте
    {
        return isset($_POST['Page']) && !$GLOBALS['cookiesmanagement']->isAgree;;
    }
    protected function getComposedHiddenInputs()// данный метод надо будет удалить, а потом разместить в трейте
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
    protected function getFormMessage(string $place, $condition, $class = null) // данный метод надо будет удалить, а потом разместить в трейте
    {
        if ($condition) {
            return "<div class='$class'>{$this->getText($this->lang, $place)}</div>";
        }
    }
    protected const ARR_FOR_LANG = [
        ['ru', 'rus', 'РУС'], 
        ['en', 'eng', 'ENG']
    ]; // данную константу надо будет куда-нибудь засунуть
    private function getInputTextForComposeHTML($name, $placeholder) {
        return $this->getInput('text', $this->saveInputValue($name), $name, "profile-input-block__input" , $placeholder);
    }
    private function getSelectForComposeHTML()
    {
        $valutas = [$GLOBALS["rub"] => 'Ruble', $GLOBALS["usd"] => 'Dollar', $GLOBALS["eur"] => 'Euro'];
        $select = "<select class='profile-input-block__input' name='Valuta' size='1'>";
        
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
    private function getSelectorForComposerHTMLlang()
    {
        $select = "<select class='profile-input-block__input' name='Lang' size='1'>";
        if (!isset($_POST['Lang']) || $_POST['Lang'] === '') {
            $select .= "<option disabled selected value='empty'>{$this->getText($this->lang, 'ChooseLangMain')}</option>";
        }
        foreach (self::ARR_FOR_LANG as $elem) {
            $selected = '';
            if (isset($_POST['Lang']) && $_POST['Lang'] == $elem[0]) {
                $selected = 'selected';
            }
            $select .= "<option value='$elem[0]' $selected >{$this->getText($this->lang, $elem[1] . 'Main')}</option>";
        }
        return $select .= " </select>";
    }
    private function composeHTML() // данный метод отличается от регистрационного
    {
        $registrationInput = self::INPUT_ATTRIBUTE_NAME['reregistration'];
        $telRegistrationInput = $registrationInput['tel'];
        $valutaRegistrationInput = $registrationInput['valuta'];
        $langRegistrationInput = $registrationInput['lang'];
        $hiddenInputs = $this->getComposedHiddenInputs();
        return <<<HTML
        {$this->getFacadeHeader()}
        {$this->getNavigation()}
        <article class="main__content content content_profile">
            <h1 class='content__title'>{$this->getText($this->lang, 'FormtitleMain')}</h1>
            <ul class='content__profileList profileList'>
                <li class='profile__element profile__element_tel'>{$this->getText($this->lang, 'TelMain')} -- {$this->profile['Tel']}</li>
                <li class='profile__element profile__element_email'>{$this->getText($this->lang, 'EmailMain')} -- {$this->profile['Email']}</li>
                <li class='profile__element profile__element_valuta'>{$this->getText($this->lang, 'ValutaMain')} -- {$this->profile['Valuta']}</li>
            </ul>
            <form class="content__form profile-form" method="POST" action="/profile">
                <label class="profile-form__input-block profile-input-block">
                    <div class="profile-input-block__discription">{$this->getText($this->lang, 'DiscriptionForValutaMain')}</div>
                    {$this->getSelectorForComposerHTMLlang()}
                    <span class="profile-input-block__whatisit">{$this->getText($this->lang, 'LanguageMain')}</span>
                </label>
                <label class="profile-form__input-block profile-input-block">
                    {$this->getFormMessage('TelNot11Main', $this->messageTelNot11, 'profile-input-block__message')}
                    {$this->getFormMessage('TelNotOnlyMain', $this->messageTelNotOnly, 'profile-input-block__message')}
                    <div class="profile-input-block__discription">{$this->getText($this->lang, 'DiscriptionForTelMain')}</div>
                    {$this->getInputTextForComposeHTML($telRegistrationInput, $this->telExample)}
                    <span class="profile-input-block__whatisit">{$this->getText($this->lang, 'TelMain')}</span>
                </label>
                <label class="profile-form__input-block profile-input-block">
                    <div class="profile-input-block__discription">{$this->getText($this->lang, 'DiscriptionForValutaMain')}</div>
                    {$this->getSelectForComposeHTML()}
                    <span class="profile-input-block__whatisit">{$this->getText($this->lang, 'ValutaMain')}</span>
                </label>
                {$this->getSubmitWithoutLang($this->getText($this->lang, 'SendMain'), $this->pageName, "profile-form__submit")}
            </form>
        </article>
        {$this->getFacadeFooter()}
HTML;
    }

    function getHTML()
    {
        return $this->composeHTML();
    }

    private $telExample = '71001234567';
}