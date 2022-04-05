<?php
class PageCustomerCabinetAllparameters extends PageCustomerCabinet
{
    protected $pageName = '/allparameters';

    private $readingDeviceList;
    private $vehicleType;
    private $vehicleBrand;
    private $vehicleModel;
    private $ecu;

    private $messageChosenNothingReadingDevice;
    private $messageChosenNothingService;
    private $messageFileTooLarge;

    private $servicePriceList;
    
    function __construct($name, $customer_id, $email, $coins, $readingDeviceList, $servicePriceList, $vehicleType, $vehicleBrand, $vehicleModel, $ecu, bool $messageChosenNothingReadingDevice, bool $messageChosenNothingService, bool $messageFileTooLarge)
    {
        $this->dictionaryMain = $this->composeDictionaryMain();
        $this->vehicleType = $vehicleType;
        $this->vehicleBrand = $vehicleBrand;
        $this->vehicleModel = $vehicleModel;
        $this->ecu = $ecu;
        $this->servicePriceList = $servicePriceList;
        $this->readingDeviceList = $readingDeviceList;
        $this->messageChosenNothingReadingDevice = $messageChosenNothingReadingDevice;
        $this->messageChosenNothingService = $messageChosenNothingService;
        $this->messageFileTooLarge = $messageFileTooLarge;
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
            'Back' => [
                'en' => 'Back',
                'ru' => 'Назад'
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
            'SelectReadingDevice' => [
                'en' => 'Select reading device',
                'ru' => 'Выберите считывающее устройство'
            ],
            'SelectRestOptions' => [
                'en' => 'Select the rest of the options',
                'ru' => 'Выберите оставшиеся параметры'
            ],
            'You have not selected a reader' => [
                'en' => 'You have not selected a reader',
                'ru' => 'Вы не выбрали считывающее устройство'
            ],
            'You have not chosen any service' => [
                'en' => 'You have not chosen any service',
                'ru' => 'Вы никакую услугу не выбрали'
            ],
            'Your file is too large'=> [
                'en' => 'Your file is too large',
                'ru' => 'Ваш файл слишком большой'
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
    private function getMessage($seen, $message)
    {
        return ($message) ? $this->getText($this->lang, $message) : '';
    }
    
    private function composeOptionElements()
    {
        $html = "<option disabled selected value='initial-state'>{$this->getText($this->lang, 'SelectReadingDevice')}</option>";
        foreach ($this->readingDeviceList as $readingDevice) {
            $html .= "<option value = '{$this->prepareHTMLAttr($readingDevice['constant_value_value'])}'>{$readingDevice['constant_value_value']}</option>";
        }
        return $html;
    }
    private function composeInputElements()
    {
        $html = '';
        foreach ($this->servicePriceList as $servicePrice) {
            $html .= "<label class='block-checkboxes__block-checkbox block-checkbox'><input type='checkbox' name='{$this->prepareHTMLAttr($servicePrice['service_name'])}'>{$servicePrice['service_name']}<span class='block-checkbox__price'>{$servicePrice['condition_service_price']}</span></label>";
        }
        return $html;
    }
    function getServiceSet() 
    {
        $delimeter = ', ';
        $set = '';
        $servicePriceListLength = count($this->servicePriceList);
        $count = 0;
        foreach ($this->servicePriceList as $servicePrice) {
            $count++;
            if ($count < $servicePriceListLength) {
                $set .= $servicePrice['service_name'] . $delimeter;
            } else {
                $set .= $servicePrice['service_name'];
            }
        }
        return $set;
    }
    private function arrForHiddenInputBlock()
    {
        return [
            ['name' => 'vehicle_type', 'value' => $this->vehicleType],
            ['name' => 'vehicle_brand', 'value' => $this->vehicleBrand],
            ['name' => 'vehicle_model', 'value' => $this->vehicleModel],
            ['name' => 'ecu', 'value' => $this->ecu]
        ];
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getFacadeHeader()}
        {$this->getNavigation()}
        
        <article class="main__content content content_choice-file content_choice-file__last">
            <h1 class="content__title">{$this->getText($this->lang, 'SelectRestOptions')}</h1>
            <div class="content__block-selected-file block-selected-file">
                <div class="block-selected-file__property">{$this->getText($this->lang, 'VehicleType')} - <span class="block-selected-file__value" id="selected-type">{$this->getText($this->lang, $this->getDictionaryMark($this->vehicleType, 'Main'))}</span></div>
                <div class="block-selected-file__property">{$this->getText($this->lang, 'VehicleBrand')} - <span class="block-selected-file__value" id="selected-brand">$this->vehicleBrand</span></div>
                <div class="block-selected-file__property">{$this->getText($this->lang, 'VehicleModel')} - <span class="block-selected-file__value" id="selected-model">$this->vehicleModel</span></div>
                <div class="block-selected-file__property">{$this->getText($this->lang, 'SelectedECU')} - <span class="block-selected-file__value" id="selected-details">$this->ecu</span></div>
                {$this->getNormalLinkNoLang('/ecu', $this->getText($this->lang, 'Back'), $this->pageName, [['name' => 'vehicle_type', 'value' => $this->vehicleType], ['name' => 'vehicle_brand', 'value' => $this->vehicleBrand], ['name' => 'vehicle_model', 'value' => $this->vehicleModel]], 'content__wrapper-blocks',  "block-select-file__button button button_back")}
            </div>
            <form class='content__wrapper-blocks' method="POST" action='/allparameters' enctype="multipart/form-data" id='form_treatment_file'>
                <div class="content__block-select-options block-select-options" id="block-select-options">
                    <div class="block-select-options__option">
                        <label class="block-select-options__block">{$this->getText($this->lang, 'PlateOfVehicleOptional')}
                            <input id="plate-vehicle" type="text" placeholder="00 XXX 00" name="plate_vehicle">
                        </label>
                    </div>
                
                    <div class="block-select-options__option">
                        <label class="block-select-options__block">{$this->getText($this->lang, 'VINVehicleIdentificationNumberOptional')}
                            <input id="vin-vehicle-identification-number" type="text" placeholder="0000-xxxx-0000-xxxx" name="vin">
                        </label>
                    </div>
                
                    {$this->getFormMessage('You have not selected a reader', $this->messageChosenNothingReadingDevice, 'form__message_red')}
                    <div class="block-select-options__option">
                        <label class="block-select-options__block" for="reading-device">{$this->getText($this->lang, 'ReadingDevice')}
                            <select id="reading-device" name="reading_device">
                                {$this->composeOptionElements()}
                            </select>
                        </label>
                    </div>
                

                    {$this->getFormMessage('Your file is too large', $this->messageFileTooLarge, 'form__message_red')}
                    <div class="block-select-options__option">
                        <label class="block-select-options__block" for="vehicle-original-file">{$this->getText($this->lang, 'OriginalVehicleFileRequired')}
                            <input type="hidden" name="MAX_FILE_SIZE" value="{$GLOBALS['fileSizeFromCustomer']}">
                            <input id="vehicle-original-file" type="file" name="original_file">
                        </label>
                    </div>

                    {$this->getFormMessage('You have not chosen any service', $this->messageChosenNothingService, 'form__message_red')}
                    <div class="block-select-options__block-checkboxes block-checkboxes" id="block_list_services">
                        {$this->composeInputElements()}
                    </div>
                    <div class="block-select-options__total-sum total-sum-block">
                        <p class="total-sum-block__text">{$this->getText($this->lang, 'TotalAmountInCoins')}: <span class="total-sum-block__sum" id="total-sum">0</span></p>
                        <input type="hidden" name="total_sum" value="0" id="total-sum-input">
                    </div>
                    <div class="block-select-options__comment">
                        <textarea name="comment" placeholder="{$this->getText($this->lang, 'IfYouHaveAnyQuestionsWrite')}"></textarea>
                    </div>
                </div>
                    {$this->getHiddenInputBlock($this->arrForHiddenInputBlock())}
                    <input id="checksum" type="hidden" name="checksum">
                    <input type='hidden' name='lang' value='$this->lang'>
                    <input type='hidden' name="Page" value='$this->pageName'>
                    <input type='hidden' name='ServiceSet' value='{$this->getServiceSet()}'>
                </form>
                <div id='submit-file' class='button button_form hide'>{$this->getText($this->lang, 'SendMain')}</div>
        </article>
        {$this->getFacadeFooter()}
HTML;
    }
    function getHTML()
    {
        return $this->composeHTML();
    }
}