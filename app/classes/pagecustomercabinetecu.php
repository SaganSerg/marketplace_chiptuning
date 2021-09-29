<?php
class PageCustomerCabinetEcu extends PageCustomerCabinet
{
    protected $pageName = '/ecu';

    private $ecuList;
    private $vehicleType;
    private $vehicleBrand;
    private $vehicleModel;

    private $messageChosenNothing;
    
    function __construct($name, $customer_id, $email, $coins, $ecuList, $vehicleType, $vehicleBrand, $vehicleModel, $messageChosenNothing = false)
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        $this->ecuList = $ecuList;
        $this->vehicleType = $vehicleType;
        $this->vehicleBrand = $vehicleBrand;
        $this->vehicleModel = $vehicleModel;
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
            'SelectVehicleModel' => [
                'en' => 'Select vehicle model',
                'ru' => 'Выберите модель транспортного средства'
            ],
            'SelectECU' => [
                'en' => 'Select ECU',
                'ru' => 'Выберите ECU'
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
    protected function prepareHTMLAttr($string)
    {
        return str_replace(' ', '_', $string);
    }
    private function composeOptionElements()
    {
        $html = "<option disabled selected value='initial-state'>{$this->getText($this->lang, 'SelectECU')}</option>";
        foreach ($this->ecuList as $ecu) {
            $html .= "<option value='{$this->prepareHTMLAttr($ecu)}'>$ecu</option>";
        }
        return $html;
    }
    private function arrForHiddenInputBlock()
    {
        return [
            ['name' => 'vehicle_type', 'value' => $this->vehicleType],
            ['name' => 'vehicle_brand', 'value' => $this->vehicleBrand],
            ['name' => 'vehicle_model', 'value' => $this->vehicleModel]
        ];
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getFacadeHeader()}
        {$this->getNavigation()}
        
        <article class="main__content content content_choice-file">
            <h1 class="content__title">{$this->getText($this->lang, 'SelectECU')}</h1>
            <div class="content__block-selected-file block-selected-file">
                <div class="block-selected-file__property">{$this->getText($this->lang, 'VehicleType')} - <span class="block-selected-file__value" id="selected-type">{$this->getText($this->lang, $this->getDictionaryMark($this->vehicleType, 'Main'))}</span></div>
                <div class="block-selected-file__property">{$this->getText($this->lang, 'VehicleBrand')} - <span class="block-selected-file__value" id="selected-brand">$this->vehicleBrand</span></div>
                <div class="block-selected-file__property">{$this->getText($this->lang, 'VehicleModel')} - <span class="block-selected-file__value" id="selected-model">$this->vehicleModel</span></div>
                <div class="block-selected-file__property">{$this->getText($this->lang, 'SelectedECU')} - <span class="block-selected-file__value" id="selected-details">{$this->getText($this->lang, 'NotSelected')}</span></div>
            </div>
            <form class='content__wrapper-blocks' method="POST" action='/allparameters'>
                <div class="content__block-select-file block-select-file" id="block-vehicle-type">
                    <label class="block-select-file__block" >ECU
                        <select class="block-select-file__select" name="ecu">
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