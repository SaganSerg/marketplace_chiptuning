<?php
class PageCustomerCabinetDealsdeal extends PageCustomerCabinetDeals
{
    private $dealSpecification;
    private $parametersServicesMessages;
    protected $pageName = 'Dealsdeal';
    function __construct($name, $customer_id, $login, $coins, $dealSpecification, $parametersServicesMessages)
    {
        $this->dealSpecification =  $dealSpecification;
        $this->parametersServicesMessages = $parametersServicesMessages;
        $this->dictionaryMain = $this->composeDictionaryMain();
        parent::__construct($name, $customer_id, $login, $coins);
    }
    private function composeDictionaryMain() {
        return [
            'TitleMain' => [
                'en' => 'Chip tuning',
                'ru' => 'Чип-тюнинг'
            ],
            'file_processing_car_egr_off' => [
                'en' => 'Disabling the EGR system',
                'ru' => 'Отключение системы EGR'
            ],
            'file_processing_car_cat_off' => [
                'en' => 'Disabling the CAT system',
                'ru' => 'Отключение системы CAT'
            ],
            'file_processing_car_guc_artrima_mod' => [
                'en' => 'Disabling GUS ARTIMA',
                'ru' => 'Отключение GUS ARTIMA'
            ],
            'file_processing_car_others_diger_hizmetler' => [
                'en' => 'Other services',
                'ru' => 'Другие услуги'
            ],
            'file_processing_car_checksum_correction' => [
                'en' => 'Checksum collection',
                'ru' => 'Собирание чексуммы'
            ],
            'file_processing_car_dpf_off' => [
                'en' => 'Disabling the DPF system',
                'ru' => 'Отключение DPF'
            ],
            'file_processing_car_scr_adblue_off' => [
                'en' => 'Disabling the SCR ADBLUE system',
                'ru' => 'Отключение SCR ADBLUE'
            ],
            'file_processing_car_speed_limit' => [
                'en' => 'Disable speed limit',
                'ru' => 'Отключение ограничения скорости'
            ],
            'file_processing_car_dtc_off' => [
                'en' => 'Disabling the DTC system',
                'ru' => 'Отключение DTC'
            ],
            'file_processing_car_ori_file_request' => [
                'en' => 'ORI file request',
                'ru' => 'Запрос ORI файла'
            ],
            'TransactionAmountEnd' => [
                'en' => "coins",
                'ru' => "коинов"
            ],
            'TransactionAmountStart' => [
                'en' => "Transaction amount",
                'ru' => "Сумма сделки"
            ],
            'TransactionDateStart' => [
                'en' => '',
                'ru' => 'Дата регистрации сделки'
            ],
            'TransactionDateEnd' => [
                'en' => '',
                'ru' => 'по Гринвичу'
            ],
            'TransactionStatus' => [
                'en' => '',
                'ru' => 'Статус сделки'
            ],
            'unpaid' => [
                'en' => 'unpaid',
                'ru' => 'не оплачена'
            ],
            'paid' => [
                'en' => 'paid',
                'ru' => 'оплачена'
            ],
            'being done' => [
                'en' => 'being done',
                'ru' => 'делается'
            ],
            'done' => [
                'en' => 'done',
                'ru' => 'сделано'
            ],
            'unknown' => [
                'en' => 'unknown (some mistake)',
                'ru' => 'неизвестно (какая-то ошибка)'
            ],
            'ThisIsYourDeal' => [
                'en' => 'This is your deal',
                'ru' => 'Это ваша сделка'
            ],
            'VehicleType' => [
                'en' => 'A vehicle type',
                'ru' => 'Тип транспортного средства'
            ],
            'VehicleBrand' => [
                'en' => 'A vehicle brand',
                'ru' => 'Брэнд транспортного средства'
            ],
            'VehicleModel' => [
                'en' => 'A vehicle model',
                'ru' => 'Модель транспортного средства'
            ],
            'Car' => [
                'en' => 'Car',
                'ru' => 'Автомобиль'
            ],
            'Motorcycle' => [
                'en' => 'Motorcycle',
                'ru' => 'Мотоцикл'
            ],
            'Marine' => [
                'en' => 'A water transport',
                'ru' => 'Водный транспорт'
            ],
            'ConstructionAgriculturalVehicle' => [
                'en' => 'Construction and agricultural vehicle',
                'ru' => 'Строительный и сельскохозяйственный транспорт'
            ],
            'TruckBus' => [
                'en' => 'Trucks and buses',
                'ru' => 'Грузовики и автобусы'
            ],
            'SelectedECU' => [
                'en' => 'Selected ECU',
                'ru' => 'Выбранный ECU'
            ],
            'PlateOfVehicle' => [
                'en' => 'A plate of vehicle',
                'ru' => 'Плата транспортного средства'
            ],
            'IDvehicle' => [
                'en' => 'Vehicle identification number',
                'ru' => 'Идентификационный номер транспортного средства'
            ],
            'ReadableDevice' => [
                'en' => 'Readable device',
                'ru' => 'Считываемое устройство'
            ],
            'FileIsTransferred' => [
                'en' => 'The file is transferred',
                'ru' => 'Файл передан'
            ],
            'OrderedServices' => [
                'en' => 'Ordered services',
                'ru' => 'Заказанные услуги'
            ],
            'ButtonPay' => [
                'en' => 'To pay',
                'ru' => 'Оплатить'
            ],
            'ButtonSend' => [
                'en' => 'Send',
                'ru' => 'Отправить'
            ],
            'Messages' => [
                'en' => 'Messages',
                'ru' => 'Переписка'
            ],
            'CheckNewMessages' => [
                'en' => 'Check New Messages',
                'ru' => 'Проверить новые сообщения'
            ],
            'UploadFile' => [
                'en' => 'Upload File',
                'ru' => 'Скачать файл'
            ]
        ];
    }
    // {$this->dealSpecification['order_amount']}
    private function transformTypeVehicle($type)
    {
        switch ($type) {
            case 'Car': return'Car';
            case 'Motorcycle' : return 'Motorcycle';
            case 'Marine' : return 'Marine';
            case 'Construction_agricultural_vehicles' : return 'ConstructionAgriculturalVehicle';
            case 'Truck_bus' : return 'TruckBus';
            default : return 'unknown';
        }
    }
    private function getServiсeList(array $list, $class = null)
    {
        $html = '';
        foreach($list as $itemName => $itemValue) {
            if ($itemValue && $itemName != 'order_item_id') {
                $html .= "<li class='$class'>{$this->getText($this->lang, $itemName)}</li>";
            }
        }
        return $html;
    }
    private function getMessageList(array $list, $idItem, $class = null)
    {
        $html = '';
        foreach ($list as $listElem) {
            $html .= "<li class='";
            if ($listElem['message_from'] == 'customer') {
                $html .= " customer-message";
            } elseif ($listElem['message_from'] == 'provider') {
                $html .= " provider-message";
                if (!$listElem['message_seen']) {
                    $html .= " provider-message_not-seen";
                }
            }
            $html .= " $class order-message'><div class='order-message__date'>{$this->transformTime($listElem['message_date'])}</div><div class='order-message__content'>{$listElem['message_content']}</div></li>";
        }
        $html .= "
        <li class='$class order-textarea'>
            <form class='order-textarea__form' action='{$this->getRequerMyself()}' method='POST'>
                <textarea class='order-textarea__this' name='comment' placeholder='Если есть вопросы -- пишите!'></textarea>
                <input type='hidden' name='id_item' value='$idItem'>
                {$this->getSubmit($this->getText($this->lang, 'ButtonSend'), $this->pageName, 'order-textarea__submit')}
            </form>
            <form class='order-textarea__form' action='{$this->getRequerMyself()}' method='POST'>
                {$this->getSubmit($this->getText($this->lang, 'CheckNewMessages'), $this->pageName, 'order-textarea__submit')}
            </form>
        </li>";
        return $html;
    }
    private function getButtonForUploadFile($idItem)
    {
        if ($this->dealSpecification['order_status'] == 'done') {
            return "<form class='content__form-button form-button' method='POST' action='/uploadprovfile'>
                        <input type='hidden' name='id_item' value='$idItem'>
                        <input type='hidden' name='Page' value='{$this->pageName}'>
                        <button class='form-button__button' type='submit'>{$this->getText($this->lang, 'UploadFile')}</button>
                    </form>";
        }
    }
    private function getOneOrderItem($parameterArr, $serviceArr, $messageArr, $idItem)
    {
        return <<<HTML
    <ul class='content__order-parameters order-parameters'>
        <li class='order-parameters__parameter order-parameter'>{$this->getText($this->lang, 'VehicleType')} - <span class='order-parameter__value'>{$this->getText($this->lang, $this->transformTypeVehicle($parameterArr['file_processing_vehicle_type']))}</span></li>
        <li class='order-parameters__parameter order-parameter'>{$this->getText($this->lang, 'VehicleBrand')} - <span class='order-parameter__value'>{$parameterArr['file_processing_vehicle_brand']}</span></li>
        <li class='order-parameters__parameter order-parameter'>{$this->getText($this->lang, 'VehicleModel')} - <span class='order-parameter__value'>{$parameterArr['file_processing_vehicle_model']}</span></li>
        <li class='order-parameters__parameter order-parameter'>{$this->getText($this->lang, 'SelectedECU')} - <span class='order-parameter__value'>{$parameterArr['file_processing_selected_ecu']}</span></li>
        <li class='order-parameters__parameter order-parameter'>{$this->getText($this->lang, 'PlateOfVehicle')} - <span class='order-parameter__value'>{$parameterArr['file_processing_plate_of_vehicle']}</span></li>
        <li class='order-parameters__parameter order-parameter'>{$this->getText($this->lang, 'IDvehicle')} - <span class='order-parameter__value'>{$parameterArr['file_processing_vin_vehicle_identification_number']}</span></li>
        <li class='order-parameters__parameter order-parameter'>{$this->getText($this->lang, 'ReadableDevice')}- <span class='order-parameter__value'>{$parameterArr['file_processing_reading_device']}</span></li>
        <li class='order-parameters__parameter order-parameter'><span class='order-parameter__value'>{$this->getText($this->lang, 'FileIsTransferred')}</span></li>
    </ul>
    <h2 class='content__order-services-title'>{$this->getText($this->lang, 'OrderedServices')}</h2>
    <ul class='content__order-services order-services'>
        {$this->getServiсeList($serviceArr, 'order-services__service order-service')}
    </ul>
    <h2 class='content__order-messages-title'>{$this->getText($this->lang, 'Messages')}</h2>
    <ul class='content__order-messages order-messages'>
        {$this->getMessageList($messageArr, $idItem, 'order-messages__message')}
    </ul>
    {$this->getButtonForUploadFile($idItem)}
HTML;
    }
    private function getOrderItems() // надо доделать
    {
        $arr = $this->parametersServicesMessages;
        $html = '';
        foreach ($arr as $idItemName => $idItem) {
            $html = $this->getOneOrderItem($idItem['parameter_list'], $idItem['service_list'], $idItem['message_list'], $idItemName);
        }
        return $html;
    }
    private function showPayButton($classNotification, $classButtonForm, $classButtonInput)
    {
        if ($this->dealSpecification['order_status'] == 'unpaid') {
            if ($this->dealSpecification['order_amount'] <= $this->coins) {

                $html = $this->getNormalLinkNoLang($this->getRequerMyself(), $this->getText($this->lang, 'ButtonPay'), $this->pageName, 
                            [
                                ['name' => 'payService', 'value' => $this->dealSpecification['order_amount']]
                            ],
                            $classButtonForm, $classButtonInput
                        );
            } elseif ($this->dealSpecification['order_amount'] > $this->coins) {
                $html = "<div class='notificaiton-not-enough-coins $classNotification'>У вас не достаточно коинов для оплаты услуги. Вам нужно докупить коины.</div>";
            } else {
                $html = '';
            }
        } else {
            $html = '';
        }
        return $html;
    }
    private function composeHTML() 
    {
        return <<<HTML
        {$this->getFacadeHeader()}
        {$this->getNavigation()}
        
        <article class="main__content content content_deal">
            <h1 class='content__title'>{$this->getText($this->lang, 'ThisIsYourDeal')}</h1>
            <ul class='content__deal-parameters deal-parameters'>
                <li class='deal-parameters__parameter deal-parameter deal-parameter_sum'>{$this->getText($this->lang, 'TransactionAmountStart')} - <span class='deal-parameter__value deal-parameter__value_sum'>{$this->dealSpecification['order_amount']}</span> {$this->getText($this->lang, 'TransactionAmountEnd')}</li>
                <li class='deal-parameters__parameter deal-parameter deal-parameter_date'>{$this->getText($this->lang, 'TransactionDateStart')} - <span class='deal-parameter__value deal-parameter__value_date'>{$this->transformTime($this->dealSpecification['order_date'])}</span> ({$this->getText($this->lang, 'TransactionDateEnd')})
                </li>
                <li class='deal-parameters__parameter deal-parameter deal-parameter_status'>{$this->getText($this->lang, 'TransactionStatus')} - <span class='deal-parameter__value deal-parameter__value_status'>{$this->getText($this->lang, $this->transformDealStatus($this->dealSpecification['order_status']))}</span>
                {$this->showPayButton('deal-parameter__notifacation-not-enough-coins', null, 'deal-parameter__button-pay button deal-button-pay')}
                </li>
            </ul>
            
            {$this->getOrderItems()}

        </article>
        {$this->getFacadeFooter()}
HTML;
    }
    function getHTML()
    {
        return $this->composeHTML();
    }
}