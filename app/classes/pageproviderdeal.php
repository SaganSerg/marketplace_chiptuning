<?php
class PageProviderDeal extends PageProvider
{
    protected $pageName = '/deal';
    private $customerParameters;
    private $dealSpecification;
    private $parameterList;
    private $serviceList;
    private $messageList;
    private $sessionProviderId;
    private $uploadFileMessage;
    private $fileData;

    function __construct(string $name, array $customerParameters, array $dealSpecification, array $parameterList, array $serviceList, array $messageList, string $uploadFileMessage = '', $sessionProviderId = null, array $fileData = [])
    {
        parent::__construct($name);
        $this->customerParameters = $customerParameters;
        $this->dealSpecification = $dealSpecification;
        $this->parameterList = $parameterList;
        $this->serviceList = $serviceList;
        $this->messageList = $messageList;
        $this->sessionProviderId = $sessionProviderId;
        $this->uplaodFileMessage = $uploadFileMessage;
        $this->fileData = $fileData;
    }

    private function seenButton($message_id, $messageSeen)
    {
        $button = '';
        if ($this->dealSpecification['provider_id']) {
            if (!$messageSeen) {
                $button = "<form class='provider-seen-button' action='{$this->getRequerMyself()}' method='POST'>
                    <input type='hidden' name='Page' value='$this->pageName'>
                    <input type='hidden' name='customer_order_id' value='{$this->dealSpecification['customer_order_id']}'>
                    <input type='hidden' name='message_id' value='$message_id'>
                    <input type='hidden' name='button_message_seen' value='1'>
                    <input class='provider-seen-button__submit' type='submit' value='Просмотрено'>
                </form>";
            }
        }
        
        return $button;
    }
    
    private function getServiсeList($class = null)
    {
        $html = '';
        foreach($this->serviceList as $service) {
            $html .= "<li class='$class'>{$service['service_name']}</li>";
        }
        return $html;
    }

    private function getMessageList($class = null)
    {
        $html = '';
        foreach ($this->messageList as $listElem) {
            $html .= "<li class='";
            if ($listElem['message_from'] == 'customer') {
                $html .= "provider-messages__message provider-messages__message_customer";
                $seenButton = $this->seenButton($listElem['message_id'], $listElem['message_seen']);
            } elseif ($listElem['message_from'] == 'provider') {
                $html .= "provider-messages__message provider-messages__message_provider";
                $seenButton = '';
            }
            $html .= " $class'><div>{$this->dateFromTimestampDMYHMS($listElem['message_date'])}</div><div class=''>{$listElem['message_content']}</div>$seenButton</li>";
        }
        if ($this->dealSpecification['provider_id']) {
            $html .= "
                <li class='$class'>
                    <form class='provider-form-textarea' action='{$this->getRequerMyself()}' method='POST'>
                        <textarea class='provider-form-textarea__field' name='message_content'></textarea>
                        <input type='hidden' name='Page' value='$this->pageName'>
                        <input type='hidden' name='customer_order_id' value='{$this->dealSpecification['customer_order_id']}'>
                        <input class='provider-form-textarea__submit' type='submit' value='Отправить'>
                    </form>
                </li>";
        }
        return $html;
    }
    
    private function getButtonForLoadFile()
    {
        if ($this->dealSpecification['provider_id']) {
            return "
            <form class='provider-download-treated-file-block' method='POST' action='/downloadprovfile' enctype='multipart/form-data'>
                <fieldset class='provider-download-treated-file-form__block'>
                    <legend class='provider-download-treated-file-form__title'>Загрузка обработанного файла</legend>
                    <div class='provider-download-treated-file-form__message'>$this->uplaodFileMessage</div>
                    <input type='hidden' name='Page' value='$this->pageName'>
                    <input type='hidden' name='customer_order_id' value='{$this->dealSpecification['customer_order_id']}'>
                    <input type='hidden' name='MAX_FILE_SIZE' value='{$GLOBALS['fileSizeFromCustomer']}'>
                    <label class='provider-download-treated-file-form__button-choose-file'>
                    Выбрать файл
                        <input class='hide' type='file' name='treatedfile' id='treated-file'>
                    </label>
                    <input id='checksum-treated-file' type='hidden' name='checksumtreatedfile'>
                    <button class='provider-download-treated-file-form__button-upload' type='submit'>Загрузить файл</button>
                </fieldset>
            </form>
        ";
        }
    }

    private function getDownloadingFileChecksumButton()
    {
        if ($this->dealSpecification['provider_id']) {
            $checksum = isset($this->fileData['file_path_checksum']) ? $this->fileData['file_path_checksum'] : 'No file';  
            return "
            <div class='provider-checksum-block'>
                <h3 class='provider-checksum-block__title'>Проверка корректности скаченного файла</h3>
                <div id='downloading-file-checksum' class='hide'>$checksum</div>
                <div class='provider-checksum-block__message provider-checksum-block__message_ok hide' id='downloading-file-checksum-message-ok'>Контрольные суммы совпали. Клиентский файл скачен без проблем</div>
                <div class='provider-checksum-block__message provider-checksum-block__message_trouble hide' id='downloading-file-checksum-message-trouble'>Контрольные суммы не совпали. Клиентский файл нужно скачать еще раз и еще раз проверить контрольные суммы</div>
                <div class='provider-checksum-block__message provider-checksum-block__message_trouble hide' id='downloading-file-no-file'>Вы проверяете какой-то левый файл. На данной сделке еще нет файла от клиента</div>
                <label class='provider-checksum-block__submit'>
                Выбрать файл
                <input class='hide' type='file' name='downloadingfile' id='downloading-file'>
                </label>
            </div>
        ";
        }
    }

    private function getLinkForDownloadFile()
    {
        if ($this->dealSpecification['provider_id']) {
            return "
            <form class='provider-button-download' method='POST' action='/uploadcustfile'>
                <fieldset class='provider-button-download__block'>
                    <legend class='provider-button-download__title'>Скачать файл заказчика</legend>
                    <input type='hidden' name='Page' value='$this->pageName'>
                    <input type='hidden' name='link-download' value='{$this->dealSpecification['customer_order_id']}'>
                    <button class='provider-button-download__submit' type='submit'>Скачать файл заказчика</button>
                </fieldset>
            </form>
        ";
        }
    }
    
    private function getOrder()
    {
        $parameterListHTML = '';
        foreach ($this->parameterList as $parameter) {
            $parameterListHTML .= "<li class='provider-deal-parameters__element'>" . $parameter['customer_order_data_name'] . " - <span class='provider-deal-parameters__element-value'>" . $parameter['customer_order_data_value'] . "</span></li>"; 
        }
        return <<<HTML
            <div class='content-provider-deal__parameters provider-deal-parameters'>
                <h2 class='provider-deal-parameters__title'>Технические данные сделки</h2>
                <ul class='provider-deal-parameters__list'>
                    $parameterListHTML
                </ul>
            </div>
            <div class='content-provider-deal__ordered provider-deal-ordered'>
                <h2 class='provider-deal-ordered__title'>Заказанные услуги</h2>
                <ul class='provider-deal-ordered__list'>
                    {$this->getServiсeList('provider-deal-ordered__element')}
                </ul>
            </div>
            {$this->getLinkForDownloadFile()}
            {$this->getDownloadingFileChecksumButton()}
            {$this->getButtonForLoadFile()}
            
            <h2 class='provider-messages__title'>Переписка</h2>
            <ul class='provider-messages__messages'>
                {$this->getMessageList('provider-messages__element')}
            </ul>
HTML;
    }
    private function checkStatus() {
        if ($this->dealSpecification['provider_id']) {
            return <<<HTML
                        <form class='provider-button-change-status' method='POST' action='{$this->getRequerMyself()}'>
                            <input type='hidden' name='Page' value='$this->pageName'>
                            <input type='hidden' name='customer_order_id' value='{$this->dealSpecification["customer_order_id"]}'>
                            <label><input type='radio' name='customer_order_status' value='{$GLOBALS["beingDoneDealStatus"]}'>В работе</label>
                            <label><input type='radio' name='customer_order_status' value='{$GLOBALS["doneDealStatus"]}'>Работа выполнена</label>
                            <label><input type='radio' name='customer_order_status' value='{$GLOBALS["paidDealStatus"]}' checked >Оплачена</label>
                            <input class='provider-button-change-status_submit' type='submit' value='Изменить статус'>
                        </form>
HTML;
        }
    }
    private function getDoDealMainButton()
    {
        if ($this->sessionProviderId && !$this->dealSpecification['provider_id']) {
            return <<<HTML
                <form class='provider-button-make-my-deal-form' method='POST' action='{$this->getRequerMyself()}'>
                    <input type='hidden' name='Page' value='$this->pageName'>
                    <input type='hidden' name='provider_id' value='$this->sessionProviderId'>
                    <input type='hidden' name='customer_order_id' value='{$this->dealSpecification["customer_order_id"]}'>
                    <input class='provider-button-make-my-deal-form__submit' type='submit' value='Сделать моей сделкой'>
                </form>
HTML;
        }
    }
    private function composeHTML() 
    {
        $pay_date = $this->dealSpecification['customer_order_pay_date'] === null ? 'Оплаты не было' : $this->dateFromTimestampDMY($this->dealSpecification['customer_order_pay_date']);
        $provider = $this->dealSpecification['provider_id'] === null ? 'Ни за кем не закреплена' : $this->dealSpecification['provider_id'];
        return <<<HTML
        {$this->getHeader()}
        <article class='content-provider-deal'>
            <h1 class='content-provider-deal__title'>Карточка сделки</h1>
            <div class='content-provider-deal__customer-data provider-customer-data'>
                <h2 class="provider-customer-data__title">Данные клиента</h2>
                <ul class="provider-customer-data__data-list">
                    <li class='provider-customer-data__data-element'>ID клиента - {$this->customerParameters['customer_id']}</li>
                    <li class='provider-customer-data__data-element'>Email клиента - {$this->customerParameters['customer_email']}</li>
                    <li class='provider-customer-data__data-element'>Телефон клинта - {$this->customerParameters['customer_telephone']}</li>
                    <li class='provider-customer-data__data-element'>Валюта - {$this->customerParameters['customer_valuta']}</li>
                    <li class='provider-customer-data__data-element'>Язык клиента - {$this->customerParameters['customer_language']}</li>
                    <li class='provider-customer-data__data-element'>Дата регистрации клиента - {$this->dateFromTimestampDMY ($this->customerParameters['customer_registration_date'])}</li>
                    <li class='provider-customer-data__data-element'>Количество коинов которыми располагает клиент - {$this->customerParameters['customer_coins']}</li>
                </ul>
            </div>
            <div class="content-provider-deal__deal-data provider-deal-data">
                <h2 class="provider-deal-data__title">Данные сделки</h2>
                <ul class='provider-deal-data__data-list'>
                    <li class='provider-deal-data__data-element'>ID сделки - <span class=''>{$this->dealSpecification['customer_order_id']}</span></li>
                    <li class='provider-deal-data__data-element'>Тип услуги - <span class=''>{$this->dealSpecification['service_type_id']}</span></li>
                    <li class='provider-deal-data__data-element'>Сумма сделки - <span class=''>{$this->dealSpecification['customer_order_amount']}</span></li>
                    <li class='provider-deal-data__data-element'>Дата сделки - <span class=''>{$this->dateFromTimestampDMY($this->dealSpecification['customer_order_date'])}</span></li>
                    <li class='provider-deal-data__data-element'>Статус - <span class=''>{$this->dealSpecification['customer_order_status']}</span></li>
                    <li class='provider-deal-data__data-element'>За кем закреплена сделка - <span class=''>$provider{$this->getDoDealMainButton()}</span></li>
                    <li class='provider-deal-data__data-element'>Дата оплаты сделки - <span class=''>$pay_date</span></li>
                    <li class='provider-deal-data__data-element'>
                        {$this->checkStatus()}
                    </li>
                </ul>
            </div>
            {$this->getOrder()}
        </article>
        {$this->getFooter()}
    
HTML;

    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}