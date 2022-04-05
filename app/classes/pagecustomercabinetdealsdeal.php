<?php
class PageCustomerCabinetDealsdeal extends PageCustomerCabinetDeals
{
    private $dealSpecification;
    private $messages;
    private $customerOrderData;
    private $customerOrderService;
    private $customer_order_id;
    protected $pageName = '/dealsdeal';
    function __construct($name, $customer_id, $email, $coins, $dealSpecification, $messages, $customerOrderData, $customerOrderService, $customer_order_id)
    {
        $this->dealSpecification =  $dealSpecification;
        $this->messages = $messages;
        $this->customerOrderData = $customerOrderData;
        $this->customerOrderService = $customerOrderService;
        $this->customer_order_id = $customer_order_id;
        $this->dictionaryMain = $this->composeDictionaryMain();
        parent::__construct($name, $customer_id, $email, $coins);
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
            'vehicle_type' => [
                'en' => 'A vehicle type',
                'ru' => 'Тип транспортного средства'
            ],
            'VehicleBrand' => [
                'en' => 'A vehicle brand',
                'ru' => 'Брэнд транспортного средства'
            ],
            'vehicle_brand' => [
                'en' => 'A vehicle brand',
                'ru' => 'Брэнд транспортного средства'
            ],
            'VehicleModel' => [
                'en' => 'A vehicle model',
                'ru' => 'Модель транспортного средства'
            ],
            'vehicle_model' => [
                'en' => 'A vehicle model',
                'ru' => 'Модель транспортного средства'
            ],
            
            'Car' => [
                'en' => 'Car',
                'ru' => 'Автомобиль'
            ],
            'car' => [
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
            'ecu' => [
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
            ],
            'You do not have enough coins to pay for the service. You need to buy coins.' => [
                'en' => 'You do not have enough coins to pay for the service. You need to buy coins.',
                'ru' => 'У вас не достаточно коинов для оплаты услуги. Вам нужно докупить коины.'
            ],
            'egr off' => [
                'en' => 'EGR off',
                'ru' => 'Отключение EGR'
            ],
            'cat off' => [
                'en' => 'CAT off',
                'ru' => 'Отключение CAT'
            ],
            'Something went wrong' => [
                'en' => 'Something went wrong',
                'ru' => 'Что-то пошло не так'
            ],
            'If you have any questions - write!' => [
                'en' => 'If you have any questions - write!',
                'ru' => 'Если есть вопросы - пишите!'
            ]
        ];
    }
    private function getMessageList()
    {
        $html = '';
        foreach ($this->messages as $message) {
            $html .= "<li class='";
            if ($message['message_from'] == 'customer') {
                $html .= " customer-message";
            } elseif ($message['message_from'] == 'provider') {
                $html .= " provider-message";
                if (!$message['message_seen']) {
                    $html .= " provider-message_not-seen";
                }
            }
            else {
                return $this->getText($this->lang, 'Something went wrong');
            }
            $html .= " order-messages__message order-message'><div class='order-message__date'>{$this->transformTime($message['message_date'])}</div><div class='order-message__content'>{$message['message_content']}</div></li>";
            
        }
        $html .= "
            <li class='order-messages__message order-textarea'>
                <form class='order-textarea__form' action='{$this->getRequerMyself()}' method='POST'>
                    <textarea class='order-textarea__this' name='comment' placeholder='{$this->getText($this->lang, 'If you have any questions - write!')}'></textarea>
                    {$this->getSubmit($this->getText($this->lang, 'ButtonSend'), $this->pageName, 'order-textarea__submit')}
                </form>
                <form class='order-textarea__form' action='{$this->getRequerMyself()}' method='POST'>
                    {$this->getSubmit($this->getText($this->lang, 'CheckNewMessages'), $this->pageName, 'order-textarea__submit')}
                </form>
            </li>
            ";
        return $html;
    }
    private function getServiceList()
    {
        $html = '';
        foreach($this->customerOrderService as $elementCustomerOrderService) {
            $html .= "<li class='order-services__service order-service'>{$this->getText($this->lang, $elementCustomerOrderService['service_name'])}</li>";
        }
        return $html;
    }
    private function getDataList()
    {
        $html = '';
        foreach ($this->customerOrderData as $elementCustomerOrderData) {
            $html .= "<li class='order-parameters__parameter order-parameter'>{$this->getText($this->lang, $elementCustomerOrderData['customer_order_data_name'])} - <span class='order-parameter__value'>{$this->getText($this->lang, $elementCustomerOrderData['customer_order_data_value'])}</span></li>";
        }
        return $html;
    }
    private function getButtonForUploadFile()
    {
        if ($this->dealSpecification['customer_order_status'] == 'done') {
            return "<form class='content__form-button form-button' method='POST' action='/uploadprovfile'>
                        <input type='hidden' name='Page' value='{$this->pageName}'>
                        <input type='hidden' name='customer_order_id' value='{$this->customer_order_id}'>
                        <button class='form-button__button' type='submit'>{$this->getText($this->lang, 'UploadFile')}</button>
                    </form>";
        }
    }
    private function  getOrder()
    {
        return <<<HTML
        <ul class='content__order-parameters order-parameters'>
            {$this->getDataList()}
        </ul>
        <h2 class='content__order-services-title'>
            {$this->getText($this->lang, 'OrderedServices')}
        </h2>
        <ul class='content__order-services order-services'>
            {$this->getServiceList()}
        </ul>
        <h2 class='content__order-messages-title'>
            {$this->getText($this->lang, 'Messages')}
        </h2>
        <ul class='content__order-messages order-messages'>
            {$this->getMessageList()}
        </ul>
        {$this->getButtonForUploadFile()}
HTML;
    }
    private function showPayButton($classNotification, $classButtonForm, $classButtonInput)
    {
        if ($this->dealSpecification['customer_order_status'] == 'unpaid') {
            if ($this->dealSpecification['customer_order_amount'] <= $this->coins) {

                $html = $this->getNormalLinkNoLang($this->getRequerMyself(), $this->getText($this->lang, 'ButtonPay'), $this->pageName, 
                            [
                                ['name' => 'payService', 'value' => $this->dealSpecification['customer_order_amount']]
                            ],
                            $classButtonForm, $classButtonInput
                        );
            } elseif ($this->dealSpecification['customer_order_amount'] > $this->coins) {
                $html = "<div class='notificaiton-not-enough-coins $classNotification'>{$this->getText($this->lang, 'You do not have enough coins to pay for the service. You need to buy coins.')}</div>";
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
                <li class='deal-parameters__parameter deal-parameter deal-parameter_sum'>{$this->getText($this->lang, 'TransactionAmountStart')} - <span class='deal-parameter__value deal-parameter__value_sum'>{$this->dealSpecification['customer_order_amount']}</span> {$this->getText($this->lang, 'TransactionAmountEnd')}</li>
                <li class='deal-parameters__parameter deal-parameter deal-parameter_date'>{$this->getText($this->lang, 'TransactionDateStart')} - <span class='deal-parameter__value deal-parameter__value_date'>{$this->transformTime($this->dealSpecification['customer_order_date'])}</span> ({$this->getText($this->lang, 'TransactionDateEnd')})
                </li>
                <li class='deal-parameters__parameter deal-parameter deal-parameter_status'>{$this->getText($this->lang, 'TransactionStatus')} - <span class='deal-parameter__value deal-parameter__value_status'>{$this->getText($this->lang, $this->transformDealStatus($this->dealSpecification['customer_order_status']))}</span>
                {$this->showPayButton('deal-parameter__notifacation-not-enough-coins', null, 'deal-parameter__button-pay button deal-button-pay')}
                </li>
            </ul>
            
            {$this->getOrder()}

        </article>
        {$this->getFacadeFooter()}
HTML;
    }
    function getHTML()
    {
        return $this->composeHTML();
    }
}