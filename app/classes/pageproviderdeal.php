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
                $button = "<form class='' action='{$this->getRequerMyself()}' method='POST'>
                    <input type='hidden' name='Page' value='$this->pageName'>
                    <input type='hidden' name='customer_order_id' value='{$this->dealSpecification['customer_order_id']}'>
                    <input type='hidden' name='message_id' value='$message_id'>
                    <input type='hidden' name='button_message_seen' value='1'>
                    <input type='submit' value='Просмотрено'>
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
                $html .= "customer-message-for-provider";
                $seenButton = $this->seenButton($listElem['message_id'], $listElem['message_seen']);
            } elseif ($listElem['message_from'] == 'provider') {
                $html .= "provider-message-for-provider";
                $seenButton = '';
            }
            $html .= " $class'><div>{$this->dateFromTimestampDMYHMS($listElem['message_date'])}</div><div class=''>{$listElem['message_content']}</div>$seenButton</li>";
        }
        if ($this->dealSpecification['provider_id']) {
            $html .= "
                <li class='$class'>
                    <form class='' action='{$this->getRequerMyself()}' method='POST'>
                        <textarea class='' name='message_content'></textarea>
                        <input type='hidden' name='Page' value='$this->pageName'>
                        <input type='hidden' name='customer_order_id' value='{$this->dealSpecification['customer_order_id']}'>
                        <input type='submit' value='Отправить'>
                    </form>
                </li>";
        }
        return $html;
    }
    
    private function getButtonForLoadFile()
    {
        if ($this->dealSpecification['provider_id']) {
            return "
            <form method='POST' action='/downloadprovfile' enctype='multipart/form-data'>
                <div>$this->uplaodFileMessage</div>
                <input type='hidden' name='Page' value='$this->pageName'>
                <input type='hidden' name='customer_order_id' value='{$this->dealSpecification['customer_order_id']}'>
                <input type='hidden' name='MAX_FILE_SIZE' value='{$GLOBALS['fileSizeFromCustomer']}'>
                <input type='file' name='treatedfile' id='treated-file'>
                <input id='checksum-treated-file' type='hidden' name='checksumtreatedfile'>
                <button type='submit'>Загрузить файл</button>
            </form>
        ";
        }
    }

    private function getDownloadingFileChecksumButton()
    {
        if ($this->dealSpecification['provider_id']) {
            $checksum = isset($this->fileData['file_path_checksum']) ? $this->fileData['file_path_checksum'] : 'No file';  
            return "
                <div id='downloading-file-checksum' class='hide'>$checksum</div>
                <div id='downloading-file-checksum-message-ok' class='hide'>Контрольные суммы совпали. Клиентский файл скачен без проблем</div>
                <div id='downloading-file-checksum-message-trouble' class='hide'>Контрольные суммы не совпали. Клиентский файл нужно скачать еще раз и еще раз проверить контрольные суммы</div>
                <div id='downloading-file-no-file' class='hide'>Вы проверяете какой-то левый файл. На данной сделке еще нет файла от клиента</div>
                <input type='file' name='downloadingfile' id='downloading-file'>
        ";
        }
    }

    private function getLinkForDownloadFile()
    {
        if ($this->dealSpecification['provider_id']) {
            return "
            <form method='POST' action='/uploadcustfile'>
                <input type='hidden' name='Page' value='$this->pageName'>
                <input type='hidden' name='link-download' value='{$this->dealSpecification['customer_order_id']}'>
                <button type='submit'>Скачать файл</button>
            </form>
        ";
        }
    }
    
    private function getOrder()
    {
        $parameterListHTML = '';
        foreach ($this->parameterList as $parameter) {
            $parameterListHTML .= "<li class=''>" . $parameter['customer_order_data_name'] . " - <span class=''>" . $parameter['customer_order_data_value'] . "</span></li>"; 
        }
        return <<<HTML
            <ul class=''>
                $parameterListHTML
            </ul>
            <h2 class=''>Заказанные услуги</h2>
            <ul class=''>
                {$this->getServiсeList()}
            </ul>
            {$this->getLinkForDownloadFile()}
            {$this->getDownloadingFileChecksumButton()}
            {$this->getButtonForLoadFile()}
            
            <h2 class=''>Переписка</h2>
            <ul class=''>
                {$this->getMessageList()}
            </ul>
HTML;
    }
    private function checkStatus() {
        if ($this->dealSpecification['provider_id']) {
            return <<<HTML
                        <form class='' method='POST' action='{$this->getRequerMyself()}'>
                            <input type='hidden' name='Page' value='$this->pageName'>
                            <input type='hidden' name='customer_order_id' value='{$this->dealSpecification["customer_order_id"]}'>
                            <label><input type='radio' name='customer_order_status' value='{$GLOBALS["beingDoneDealStatus"]}'>В работе</label>
                            <label><input type='radio' name='customer_order_status' value='{$GLOBALS["doneDealStatus"]}'>Работа выполнена</label>
                            <label><input type='radio' name='customer_order_status' value='{$GLOBALS["paidDealStatus"]}' checked >Оплачена</label>
                            <input type='submit' value='Изменить статус'>
                        </form>
HTML;
        }
    }
    private function getDoDealMainButton()
    {
        if ($this->sessionProviderId && !$this->dealSpecification['provider_id']) {
            return <<<HTML
                <form class='' method='POST' action='{$this->getRequerMyself()}'>
                    <input type='hidden' name='Page' value='$this->pageName'>
                    <input type='hidden' name='provider_id' value='$this->sessionProviderId'>
                    <input type='hidden' name='customer_order_id' value='{$this->dealSpecification["customer_order_id"]}'>
                    <input type='submit' value='Сделать моей сделкой'>
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
        <article class='content-provider_deal'>
            <h1>Карточка сделки</h1>
            <div class=''>
                <h2>Данные клиента</h2>
                <ul>
                    <li>ID клиента - {$this->customerParameters['customer_id']}</li>
                    <li>Email клиента - {$this->customerParameters['customer_email']}</li>
                    <li>Телефон клинта - {$this->customerParameters['customer_telephone']}</li>
                    <li>Валюта - {$this->customerParameters['customer_valuta']}</li>
                    <li>Язык клиента - {$this->customerParameters['customer_language']}</li>
                    <li>Дата регистрации клиента - {$this->dateFromTimestampDMY ($this->customerParameters['customer_registration_date'])}</li>
                    <li>Количество коинов которыми располагает клиент - {$this->customerParameters['customer_coins']}</li>
                </ul>
            </div>
            <div>
                <ul class=''>
                    <li class=''>ID сделки - <span class=''>{$this->dealSpecification['customer_order_id']}</span></li>
                    <li class=''>Тип услуги - <span class=''>{$this->dealSpecification['service_type_id']}</span></li>
                    <li class=''>Сумма сделки - <span class=''>{$this->dealSpecification['customer_order_amount']}</span></li>
                    <li class=''>Дата сделки - <span class=''>{$this->dateFromTimestampDMY($this->dealSpecification['customer_order_date'])}</span></li>
                    <li class=''>Статус - <span class=''>{$this->dealSpecification['customer_order_status']}</span></li>
                    <li class=''>За кем закреплена сделка - <span class=''>$provider{$this->getDoDealMainButton()}</span></li>
                    <li class=''>Дата оплаты сделки - <span class=''>$pay_date</span></li>
                    <li class=''>
                        {$this->checkStatus()}
                    </li>
                </ul>
            </div>
        </article>
        {$this->getOrder()}
        {$this->getFooter()}
    
HTML;

    } 
    function getHTML()
    {
        return $this->composeHTML();
    }
}