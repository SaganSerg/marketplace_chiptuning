<?php
class PageCustomerCabinetBrand extends PageCustomerCabinet
{
    protected $pageName = '/brand';
    private $vehicleBrandList;
    private $vehicleType;
    private $messageChosenNothing;
    
    function __construct($name, $customer_id, $email, $coins, $vehicleBrandList, $vehicleType, $messageChosenNothing = false)
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        $this->vehicleBrandList = $vehicleBrandList;
        $this->vehicleType = $vehicleType;
        $this->messageChosenNothing = $messageChosenNothing;
        parent::__construct($name, $customer_id, $email, $coins);
    }
    private function composeDictionaryMain() {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'BuyCoins' => [
                'en' => 'Buy coins',
                'ru' => 'Купить коины'
            ],
            'SelectVehicle' => [
                'en' => 'Select vehicle',
                'ru' => 'Выбирете транспортное средство'
            ],
            'VehicleType' => [
                'en' => 'Vehicle type',
                'ru' => 'Тип транспортного средства'
            ],
            'VehicleBrand' => [
                'en' => 'Vehicle brand',
                'ru' => 'Бренд транспортного средства'
            ],
            'VehicleModel' => [
                'en' => 'Vehicle model',
                'ru' => 'Модель транспортного средства'
            ],
            'SelectedECU' => [
                'en' => 'Selected ECU',
                'ru' => 'Выбранный ECU'
            ],
            'NotSelected' => [
                'en' => 'Not selected',
                'ru' => 'Не выбран'
            ],
            'Further' => [
                'en' => 'Further',
                'ru' => 'Далее'
            ],
            'PlateOfVehicleOptional' => [
                'en' => 'Plate of Vehicle (optional)',
                'ru' => 'Плата транспортного средства (опционально)'
            ],
            'VINVehicleIdentificationNumberOptional' => [
                'en' => 'VIN Vehicle Identification Number (Optional)',
                'ru' => 'VIN идентификационный номер транспортного средства (опционально)'
            ],
            'ReadingDevice' => [
                'en' => 'Reading Device',
                'ru' => 'Считывающее устройство'
            ],
            'Other' => [
                'en' => 'Other',
                'ru' => 'Другое'
            ],
            'OriginalVehicleFileRequired' => [
                'en' => 'Original vehicle file (Required)',
                'ru' => 'Оригинальный файл транспортного средства (Обязательно)'
            ],
            'TotalAmountInCoins' => [
                'en' => 'Total amount in coins',
                'ru' => 'Итоговая сумма в коинах'
            ],
            'IfYouHaveAnyQuestionsWrite' => [
                'en' => 'If you have any questions - write!',
                'ru' => 'Если есть вопросы -- пишите!'
            ],
            'SendMain' => [
                'en' => 'Send',
                'ru' => 'Отправить'
            ],
            'SelectVehicleTypeMain' => [
                'en' => 'Select vehicle type',
                'ru' => 'Выберите тип транспортного средства'
            ],
            'SelectVehicleBrand' => [
                'en' => 'Choose a vehicle brand',
                'ru' => 'Выберите брэнд транспортного средства'
            ],
            'CarMain' => [
                'en' => 'Car',
                'ru' => 'Автомобиль'
            ],
            'MorineMain' => [
                'en' => 'Marine',
                'ru' => 'Морское судно'
            ],
            'NothingSelected' => [
                'en' => "Nothing selected",
                'ru' => 'Ничего не выбрано'
            ],
            'Back' => [
                'en' => 'Back',
                'ru' => 'Назад'
            ]
        ];
    }
    private function getDictionaryMark($string, $postfix)
    {
        $arr = explode(' ', $string );
        $mark = '';
        foreach ($arr as $elem) {
            $mark .= ucfirst($elem);
        }
        return $mark .= $postfix;
    }
    private function composeOptionElements()
    {
        $html = "<option disabled selected value='initial-state'>{$this->getText($this->lang, 'SelectVehicleBrand')}</option>";
        foreach ($this->vehicleBrandList as $vehicleBrand) {
            $vehicleBrandWithUnderscore = $this->prepareHTMLAttr($vehicleBrand);
            $html .= "<option value = '$vehicleBrandWithUnderscore'>$vehicleBrand</option>";
        }
        return $html;
    }
    private function arrForHiddenInputBlock()
    {
        return [
            ['name' => 'vehicle_type', 'value' => $this->vehicleType]
        ];
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getFacadeHeader()}
        {$this->getNavigation()}
        
        <article class="main__content content content_choice-file">
            <h1 class="content__title">{$this->getText($this->lang, 'SelectVehicleBrand')}</h1>
            <div class="content__block-selected-file block-selected-file">
                <div class="block-selected-file__property">{$this->getText($this->lang, 'VehicleType')} - <span class="block-selected-file__value" id="selected-type">{$this->getText($this->lang, $this->getDictionaryMark($this->vehicleType, 'Main'))}</span></div>
                <div class="block-selected-file__property">{$this->getText($this->lang, 'VehicleBrand')} - <span class="block-selected-file__value" id="selected-brand">{$this->getText($this->lang, 'NotSelected')}</span></div>
                <div class="block-selected-file__property">{$this->getText($this->lang, 'VehicleModel')} - <span class="block-selected-file__value" id="selected-model">{$this->getText($this->lang, 'NotSelected')}</span></div>
                <div class="block-selected-file__property">{$this->getText($this->lang, 'SelectedECU')} - <span class="block-selected-file__value" id="selected-details">{$this->getText($this->lang, 'NotSelected')}</span></div>
                {$this->getNormalLinkNoLang('/treatment', $this->getText($this->lang, 'Back'), $this->pageName, [], 'content__wrapper-blocks',  "block-select-file__button button button_back")}
            </div>
            <form class='content__wrapper-blocks' method="POST" action='/model'>
                <div class="content__block-select-file block-select-file">
                    {$this->getFormMessage('NothingSelected', $this->messageChosenNothing, 'form__message_red')}
                    <label class="block-select-file__block">{$this->getText($this->lang, 'VehicleBrand')}
                        <select class="block-select-file__select" name="vehicle_brand">
                        {$this->composeOptionElements()}
                        </select>
                    </label>
                    {$this->getHiddenInputBlock($this->arrForHiddenInputBlock())}
                    {$this->getSubmit($this->getText($this->lang, 'Further'), $this->pageName, "block-select-file__button button button_next")}
                </div>
            </form>
            
        </article>
        {$this->getFacadeFooter()}
HTML;
    }
    function getHTML()
    {
        return $this->composeHTML();
    }
}