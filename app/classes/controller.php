<?php
abstract class Controller
{
    static private $pppp = '123456';
    static private $allCustomerCabinetPage = ['/allparameters', '/brand', '/dealsdeal', '/dealsdeals', '/ecu', '/history', '/model', '/pay', '/profile', '/treatment'];
    static private $allCustomerFacadePage = ['/contacts', '/index', '/newpassword', '/notfound', '/rememberpassword', '/sentmail'];
    static private function getText($lang, $place, $dictionary) // $lang -- язык вида 'en'; $place -- это название массива являющегося элементом массива $date
    {
        if (isset($dictionary[$place][$lang])) {
            return $dictionary[$place][$lang];
        }
        // throw new Exception("Такой элемент массива не существует -- $place и $lang");
        return $place;
    }
    static function startSessionWithCheck(CookiesManagement $cookiesmanagement) {
        if (session_status() !== 2) {
            $cookiesmanagement->customerSessionStart();
            return true;
        }
        return false;
    }
    static function getProfile($mark, $cookiesmanagement, $model, $arrProfileMessage = [])
    {
        self::startSessionWithCheck($cookiesmanagement);
        $email = $model->getElements(
            "SELECT customer_email FROM customer WHERE customer_id = ?",
            [$_SESSION['customer_id']]
        )[0]['customer_email'];
        $coins = $model->getElements(
            "SELECT customer_coins FROM customer WHERE customer_id = ?",
            [$_SESSION['customer_id']]
        )[0]['customer_coins'];
        $tel = $model->getElements(
            "SELECT customer_telephone FROM customer WHERE customer_id = ?",
            [$_SESSION['customer_id']]
        )[0]['customer_telephone'];
        $valuta = $model->getElements(
            "SELECT customer_valuta FROM customer WHERE customer_id = ?",
            [$_SESSION['customer_id']]
        )[0]['customer_valuta'];
        $arrProfile = [
            'Tel' => $tel,
            'Valuta' => $valuta,
            'Email' => $email
        ];
        return (new PageCustomerCabinetProfile($mark, $_SESSION['customer_id'], $email, $coins, $arrProfile, $arrProfileMessage))->getHTML();
    }

    static function routingSimpleProfile3(array $fromPageArr, $cookiesmanagement, $model)
    {
        $toPage = '/profile';
        if ($_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists('Page', $_POST)) {
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    return self::getProfile($toPage, $cookiesmanagement, $model);
                }
            }
        }
        return self::getNotFound($toPage);
    }

    static function routingSimpleHistory3(array $fromPageArr, $cookiesmanagement, $model)
    {
        $toPage = '/history';
        if ($_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists('Page', $_POST)) {
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    self::startSessionWithCheck($cookiesmanagement);
                    unset($_SESSION['customer_order_id']);
                    $email = $model->getElements(
                        "SELECT customer_email FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_email'];
                    $coins = $model->getElements(
                        "SELECT customer_coins FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_coins'];
                    $transactions = $model->getElements(
                        "SELECT coin_transaction_id, coin_transaction_date, coin_transaction_sum, coin_transaction_status FROM coin_transaction WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    );
                    $buyList = [];
                    foreach ($transactions as $transaction) {
                        $customer_order_id = $model->getElements(
                            "SELECT customer_order_id FROM deal_payment WHERE coin_transaction_id = ?",
                            [$transaction['coin_transaction_id']]
                        );
                        $buyList[] = [
                            'coin_transaction_id' => $transaction['coin_transaction_id'],
                            'coin_transaction_date' => $transaction['coin_transaction_date'],
                            'coin_transaction_sum' => $transaction['coin_transaction_sum'],
                            'coin_transaction_status' => $transaction['coin_transaction_status'],
                            'customer_order_id' => $customer_order_id
                        ];
                    }
                    return (new PageCustomerCabinetHistory($toPage, $_SESSION['customer_id'], $email, $coins, $buyList))->getHTML();
                }
            }
        }
        return self::getNotFound($toPage);
    }

    static function routingSimpleDealsdeals3(array $fromPageArr, $cookiesmanagement, $model)
    {
        $toPage = '/dealsdeals';
        if ($_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists('Page', $_POST)) {
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    self::startSessionWithCheck($cookiesmanagement);
                    unset($_SESSION['customer_order_id']);
                    $email = $model->getElements(
                        "SELECT customer_email FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_email'];
                    $coins = $model->getElements(
                        "SELECT customer_coins FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_coins'];
                    $attributes = $model->getElements(
                        "SELECT customer_order_status, customer_order_date, customer_order_id, customer_order_amount FROM customer_order WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    );
                    return (new PageCustomerCabinetDealsdeals($toPage, $_SESSION['customer_id'], $email, $coins, $attributes))->getHTML();
                }
            }
        }
        return self::getNotFound($toPage);
    }
    static private function getDealCard3(Model $model, string $mark)
    {
        self::startSessionWithCheck($GLOBALS['cookiesmanagement']);
        $email = $model->getElements(
            "SELECT customer_email FROM customer WHERE customer_id = ?",
            [$_SESSION['customer_id']]
        )[0]['customer_email'];
        $coins = $model->getElements(
            "SELECT customer_coins FROM customer WHERE customer_id = ?",
            [$_SESSION['customer_id']]
        )[0]['customer_coins'];
        if (array_key_exists('customer_order_id', $_POST)) {
            $_SESSION['customer_order_id'] = $_POST['customer_order_id']; // надо разобраться откуда приходит запрос с таким параметром order_id
        }
        $dealSpecification = $model->getElements(
            "SELECT customer_order_amount, customer_order_date, customer_order_status FROM customer_order WHERE customer_order_id = ?",
            [$_SESSION['customer_order_id']]
        )[0];
        $messages = $model->getElements(
            "SELECT message_content, message_from, message_date, message_seen FROM message WHERE customer_order_id = ?",
            [$_SESSION['customer_order_id']]
        );
        $customerOrderData = $model->getElements(
            "SELECT customer_order_data_name, customer_order_data_value FROM customer_order_data WHERE customer_order_id = ? ",
            [$_SESSION['customer_order_id']]
        );
        $customerOrderService = $model->getElements(
            "SELECT service_name FROM customer_order_service WHERE customer_order_id = ?",
            [$_SESSION['customer_order_id']]
        );
        return (new PageCustomerCabinetDealsdeal($mark, $_SESSION['customer_id'], $email, $coins, $dealSpecification, $messages, $customerOrderData, $customerOrderService, $_SESSION['customer_order_id']))->getHTML();
    }

    static function addFile3 (int $date, int $customer_order_id, string $mark, Model $model, string $email, string $coins)
    {
        $time = date('Y-m-d-H-i-s', $date);
        $dirPathWithoutRootPath = $GLOBALS['incomingFileDir'] . '/' . $_SESSION['customer_id'] . '/' . $customer_order_id . '/' . $time;
        $dirPath = $GLOBALS['saveFilePath'] . $dirPathWithoutRootPath;
        $open_resurs = fopen($_FILES['original_file']['tmp_name'], 'rb');
        $file_is_string = fread($open_resurs, $GLOBALS['fileSizeFromCustomer']);
        if (customCRC16($file_is_string) == $_POST['checksum']) {
            if (!is_dir($dirPath)) {
                if (!mkdir($dirPath, 0777, true)) {
                    return self::getNotFound($mark);
                }
            }
            $path_to_file = $dirPath . '/' . $_FILES['original_file']['name'];
            $path_to_file_WithoutRootPath = $dirPathWithoutRootPath . '/' . $_FILES['original_file']['name'];
            if (move_uploaded_file($_FILES['original_file']['tmp_name'], $path_to_file)) {
                $model->addElements(
                    "INSERT INTO file_path (
                        customer_order_id, 
                        file_path_date, 
                        file_path_path, 
                        file_path_what_file
                        ) VALUES (?, ?, ?, ?)",
                    [
                        $model->cleanForm($customer_order_id),
                        $model->cleanForm($date),
                        $path_to_file_WithoutRootPath,
                        'from customer'
                    ]
                );
                return self::getDealCard3($model, $mark);
            }
        }
        return self::getNotFound($mark);
    }

    static function getNotFound($mark)
    {
        return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
    }

    static private function replacesSpaceWithUnderscore($string)
    {
        return str_replace(' ', '_', $string);
    }
    static private function replacesUnderscoreWithSpace($string)
    {
        return str_replace('_', ' ', $string);
    }

    static function checkMethod(string $method)
    {
        return $_SERVER['REQUEST_METHOD'] == $method;
    }
    static function checkPostMethod()
    {
        return self::checkMethod('POST');
    }

    static function checkMethodName($name, string $methodName, array $methodArr)
    {
        return $_SERVER['REQUEST_METHOD'] == $methodName && array_key_exists($name, $methodArr);
    }

    static function checkMethodValue($name, $value, array $method) // $name -- имя параметра, $value -- значение параметра, $method -- суперглобальный массив ($_POST, $_GET)
    {
        return array_key_exists($name, $method) && $method[$name] == $value;
    }
    static function checkPostMethodValue($name, $value)
    {
        return self::checkPostMethod() && self::checkMethodValue($name, $value, $_POST);
    }

    static function checkMethodPostAndPageName($pageName)
    {
        return self::checkPostMethodValue('Page', $pageName);
    }

    static function checkMethodPostAndPageNameAndReferer($pageName, $checkReferer) {
        return self::checkMethodPostAndPageName($pageName) && isset($checkReferer) && $checkReferer;
    }
    static function checkMethodPostAndPageNames(array $pageNames)
    {
        if (self::checkPostMethod()) {
            foreach($pageNames as $pageName) {
                if (self::checkMethodValue('Page', $pageName, $_POST)) {
                    return true;
                }
            }
        }
        return false;
    }

    static private function checkAnalogOr(array $arr)
    {
        return !!array_search(true, $arr);
    }

    static private function returnPageCustomerCabinetPayWithStaingId($mark, $email, $pass, Model $model, $cookiesmanagement)
    {
        $id = $model->getCustomerElementByEmailPass($email, $pass, 'customer_id');
        self::startSessionWithCheck($cookiesmanagement);
        $_SESSION['customer_id'] = $id;
        return self::returnPageCustomerCabinetPayWithoutStaingId($mark, $model);
    }

    static private function returnPageCustomerCabinetPayWithoutStaingId($mark, Model $model)
    {
        $email = $model->getCustomerEmail($_SESSION['customer_id']);
        $coins = $model->getCustomerCoins($_SESSION['customer_id']);

        return (new PageCustomerCabinetPay($mark, $_SESSION['customer_id'], $email, $coins))->getHTML();
    }

    static function routingSimplePayPage(array $fromPageArr, $cookiesmanagement, $model)
    {
        $toPage = "Pay";
        if ($_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists('Page', $_POST)) {
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    self::startSessionWithCheck($cookiesmanagement);
                    return self::returnPageCustomerCabinetPayWithoutStaingId($toPage, $model);
                }
            }
        }
        return self::getNotFound($toPage);
    }

    static function routingSimpleCabinetPage(array $fromPageArr, string $toPage, Model $model)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && array_key_exists('Page', $_POST)) {
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    if (str_starts_with($toPage, '/')) {
                        $toPageWithoutSlash = substr_replace($toPage, '', 0, 1);
                    }
                    $preparedToPage =  ucfirst($toPageWithoutSlash);
                    $class = 'PageCustomerCabinet' . $toPage;
                    self::startSessionWithCheck($GLOBALS['cookiesmanagement']);
                    $email = $model->getCustomerEmail($_SESSION['customer_id']);
                    $coins = $model->getCustomerCoins($_SESSION['customer_id']);

                    return (new $class($toPage, $_SESSION['customer_id'], $login, $coins))->getHTML();
                }
            }
        }
        return self::getNotFound($toPage);
    }

    static function getTreatmentPage(Model $model, CookiesManagement $cookiesmanagement, $mark, bool $messageAboutNothing)
    {
        $conditionIdList = $model->getConditionIdIds();
        $serviceTypeName = 'file treatment';
        $dataName = 'vehicle type';
        $idServiceType = $model->getServiceTyptId($serviceTypeName);
        $dataNameArrNotUnique = [];
        foreach ($conditionIdList as $conditionIdArr) {
            $conditionId = $conditionIdArr['condition_id_id'];
            $dataValues = $model->getDataValues($dataName, $idServiceType, $conditionId);
            if (array_key_exists(0, $dataValues)) {
                $dataNameArrNotUnique[] = $dataValues[0]['data_value'];
            }
        }
        $dataNameArr = array_unique($dataNameArrNotUnique);
        self::startSessionWithCheck($cookiesmanagement);
        $email = $model->getCustomerEmail($_SESSION['customer_id']);
        $coins = $model->getCustomerCoins($_SESSION['customer_id']);
        return (new PageCustomerCabinetTreatment($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr, $messageAboutNothing))->getHTML();
    }

    static function getBrandPage(Model $model, CookiesManagement $cookiesmanagement, $mark, bool $messageAboutNothing)
    {
        $conditionIdList = $model->getElements(
            "SELECT condition_id_id FROM condition_value WHERE data_name = 'vehicle type' AND data_value = ?",
            [$_POST['vehicle_type']]
        );
        
        $serviceTypeName = 'file treatment';
        $dataName = 'vehicle brand';
        $idServiceType = $model->getElements(
            "SELECT service_type_id FROM service_type WHERE service_type_name = ?",
            [$serviceTypeName]
        )[0]['service_type_id'];
        $dataNameArrNotUnique = [];
        foreach ($conditionIdList as $conditionIdArr) {
            $conditionId = $conditionIdArr['condition_id_id'];
            $some = $model->getElements(
                "SELECT data_value FROM condition_value WHERE data_name = ? AND service_type_id = ? AND condition_id_id = ?",
                [$dataName, $idServiceType, $conditionId]
            );
            if (array_key_exists(0, $some)) {
                $dataNameArrNotUnique[] = $some[0]['data_value'];
            }
        }
        $dataNameArr = array_unique($dataNameArrNotUnique);
        self::startSessionWithCheck($cookiesmanagement);
        $email = $model->getCustomerEmail($_SESSION['customer_id']);
        $coins = $model->getCustomerCoins($_SESSION['customer_id']);
        return (new PageCustomerCabinetBrand($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr, $_POST['vehicle_type'], $messageAboutNothing))->getHTML();
    }

    static function getModelPage(Model $model, CookiesManagement $cookiesmanagement, $mark, bool $messageAboutNothing)
    {
        $conditionIdList = $model->getElements(
            "SELECT condition_id_id FROM condition_value WHERE data_name = 'vehicle brand' AND data_value = ?",
            [$_POST['vehicle_brand']]
        );
        $serviceTypeName = 'file treatment';
        $dataName = 'vehicle model';
        $idServiceType = $model->getElements(
            "SELECT service_type_id FROM service_type WHERE service_type_name = ?",
            [$serviceTypeName]
        )[0]['service_type_id'];
        $dataNameArrNotUnique = [];
        foreach ($conditionIdList as $conditionIdArr) {
            $conditionId = $conditionIdArr['condition_id_id'];
            $some = $model->getElements(
                "SELECT data_value FROM condition_value WHERE data_name = ? AND service_type_id = ? AND condition_id_id = ?",
                [$dataName, $idServiceType, $conditionId]
            );
            if (array_key_exists(0, $some)) {
                $dataNameArrNotUnique[] = $some[0]['data_value'];
            }
        }
        $dataNameArr = array_unique($dataNameArrNotUnique);
        self::startSessionWithCheck($cookiesmanagement);
        $email = $model->getCustomerEmail($_SESSION['customer_id']);
        $coins = $model->getCustomerCoins($_SESSION['customer_id']);
        return (new PageCustomerCabinetModel($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr, $_POST['vehicle_type'], $_POST['vehicle_brand'], $messageAboutNothing))->getHTML();
    }

    static function getEcuPage(Model $model, CookiesManagement $cookiesmanagement, $mark, bool $messageAboutNothing)
    {
        $conditionIdList = $model->getElements(
            "SELECT condition_id_id FROM condition_value WHERE data_name = 'vehicle model' AND data_value = ?",
            [$_POST['vehicle_model']]
        );
        $serviceTypeName = 'file treatment';
        $dataName = 'ecu';
        $idServiceType = $model->getElements(
            "SELECT service_type_id FROM service_type WHERE service_type_name = ?",
            [$serviceTypeName]
        )[0]['service_type_id'];
        $dataNameArrNotUnique = [];
        foreach ($conditionIdList as $conditionIdArr) {
            $conditionId = $conditionIdArr['condition_id_id'];
            $some = $model->getElements(
                "SELECT data_value FROM condition_value WHERE data_name = ? AND service_type_id = ? AND condition_id_id = ?",
                [$dataName, $idServiceType, $conditionId]
            );
            if (array_key_exists(0, $some)) {
                $dataNameArrNotUnique[] = $some[0]['data_value'];
            }
        }
        $dataNameArr = array_unique($dataNameArrNotUnique);
        self::startSessionWithCheck($cookiesmanagement);
        $email = $model->getCustomerEmail($_SESSION['customer_id']);
        $coins = $model->getCustomerCoins($_SESSION['customer_id']);
        return (new PageCustomerCabinetEcu($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr, $_POST['vehicle_type'], $_POST['vehicle_brand'], $_POST['vehicle_model'], $messageAboutNothing))->getHTML();
    }

    static function getAllparametersPage(Model $model, CookiesManagement $cookiesmanagement, $mark, bool $messageChosenNothingReadingDevice, bool $messageChosenNothingService, bool $messageFileTooLarge)
    {
        $conditioinIdWorks = $model->getOneElementArr(
            "SELECT condition_id_id FROM condition_id WHERE condition_id_works = 1",
            [], 'condition_id_id'
        ); 
        $conditionIdModel = $model->getOneElementArr(
            "SELECT condition_id_id FROM condition_value WHERE data_name = 'vehicle model' AND data_value = ?",
            [$_POST['vehicle_model']], 'condition_id_id'
        );
        $conditionIdType = $model->getOneElementArr(
            "SELECT condition_id_id FROM condition_value WHERE data_name = 'vehicle type' AND data_value = ?",
            [$_POST['vehicle_type']], 'condition_id_id'
        );
        $conditionIdBrand = $model->getOneElementArr(
            "SELECT condition_id_id FROM condition_value WHERE data_name = 'vehicle brand' AND data_value = ?",
            [$_POST['vehicle_brand']], 'condition_id_id'
        );
        $conditionIdEcu = $model->getOneElementArr(
            "SELECT condition_id_id FROM condition_value WHERE data_name = 'ecu' AND data_value = ?",
            [$_POST['ecu']], 'condition_id_id'
        );
        $conditioinIdIdRaw = array_intersect($conditionIdModel, $conditionIdType, $conditionIdBrand, $conditionIdEcu, $conditioinIdWorks);
        $readingDeviceList = $model->getElements(
            "SELECT constant_value_value FROM constant_value WHERE service_type_id = (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment') AND constant_value_name = 'reading device'",
            []
        );
        if (count($conditioinIdIdRaw) != 1) {
            return self::getNotFound($mark);
        }
        $conditioinIdId = [];
        foreach ($conditioinIdIdRaw as $conditioinIdIdIndex) {
            $conditioinIdId[] = $conditioinIdIdIndex;
        }
        $servicePriceList = $model->getElements(
            "SELECT c.condition_service_price, s.service_name FROM condition_service c INNER JOIN service s ON s.service_id = c.service_id WHERE c.condition_id_id = ?",
            $conditioinIdId
        );
        self::startSessionWithCheck($cookiesmanagement);
        $email = $model->getCustomerEmail($_SESSION['customer_id']);
        $coins = $model->getCustomerCoins($_SESSION['customer_id']);
        return (new PageCustomerCabinetAllparameters($mark, $_SESSION['customer_id'], $email, $coins, $readingDeviceList, $servicePriceList, $_POST['vehicle_type'], $_POST['vehicle_brand'], $_POST['vehicle_model'], $_POST['ecu'], $messageChosenNothingReadingDevice, $messageChosenNothingService, $messageFileTooLarge))->getHTML();
    }

    static function getPage()
    {
        try {
            $model = new Model();
            $date = time();
            $arrDateLinkCreation = $model->getElements(
                "SELECT customer_password_recovery_date_of_link_creation, customer_password_recovery_id FROM customer_password_recovery WHERE customer_password_recovery_notwork = 0",
                []
            );
            // это нужно для предотвращения от атаки CSRF
            // в маршрутизаторе потом нужно бедет проверять установлен ли переменная checkReferer и проверять ee значение
            // если значение false, то значит это атака
            if (isset($_SERVER['HTTP_REFERER'])) {
                $checkReferer = (stripos($_SERVER['HTTP_REFERER'], $GLOBALS['protocol'] . '://' . $GLOBALS['domain']) === 0);
            } else {
                $checkReferer = null;
            }
            // --- 
            foreach ($arrDateLinkCreation as $dateLinkCreation) {
                $linkExpirationDate = $dateLinkCreation['customer_password_recovery_date_of_link_creation'] + $GLOBALS['saveLinkPasswordTime'];
                if ($linkExpirationDate < $date) {
                    $model->updateElements(
                        "UPDATE customer_password_recovery SET customer_password_recovery_notwork = ? WHERE customer_password_recovery_id = ?",
                        [$dateLinkCreation['customer_password_recovery_id']]
                    );
                }
            }
            $customerPasswordRecoveryId = $model->getCustomerPasswordRecoveryId($_SERVER['REQUEST_URI']);
            $customerPasswordRecoveryUrl = ($customerPasswordRecoveryId) ? $_SERVER['REQUEST_URI'] : '/wronglink';
            $email_for_registration_id = $model->getRegistrationEmailId($_SERVER['REQUEST_URI']);
            $email_for_registration_url = ($email_for_registration_id) ? $_SERVER['REQUEST_URI'] : '/wronglink';

            $cookiesmanagement = $GLOBALS['cookiesmanagement'];
            $mark = 'Notfound';
            switch ($_SERVER['REQUEST_URI']) {
                case '':
                case '/':
                case '/index':
                case '/index.php':
                case '/index.html':
                    $mark = '/index';
                break;
                case '/sentmailregistration':
                    $mark = '/sentmailregistration';
                break;
                case '/messagesentmailregistration':
                    $mark = '/messagesentmailregistration';
                break;
                case '/registration':
                    $mark = '/registration';
                break;
                case '/pay':
                    $mark = '/pay';
                break;
                case '/treatment':
                    $mark = '/treatment';
                break;
                case '/brand':
                    $mark = '/brand';
                break;
                case '/model':
                    $mark = '/model';
                break;
                case '/ecu':
                    $mark = '/ecu';
                break;
                case '/allparameters':
                    $mark = '/allparameters';
                break;
                case '/dealsdeals':
                    $mark = '/dealsdeals';
                break;
                case '/dealsdeal':
                    $mark = '/dealsdeal';
                break;
                case '/history':
                    $mark = '/history';
                break;
                case '/profile':
                    $mark = '/profile';
                break;
                case '/rememberpassword':
                    $mark = '/rememberpassword';
                break;
                case '/sentmail':
                    $mark = '/sentmail';
                break;
                case '/sentmailregistration':
                    $mark = '/sentmailregistration';
                case $customerPasswordRecoveryUrl :
                    $mark = $customerPasswordRecoveryUrl;
                break;
                case $email_for_registration_url :
                    $mark = $email_for_registration_url;
                break;
            }
/* 
При описании маршрутизации страниц из внешней части нужно обязательно делать ссылку на саму себя, для переключения языка
*/
            
            if ($mark == '/index') {
                if ($_SERVER['REQUEST_METHOD'] == "GET") {
                    return (new PageCustomerFacadeIndex($mark, null))->getHTML();
                }
                if ($_SERVER['REQUEST_METHOD'] == "POST") {
                    return (new PageCustomerFacadeIndex($mark, null))->getHTML();
                }
                if (self::checkMethodPostAndPageName('/index')) {
                    return (new PageCustomerFacadeIndex($mark, null))->getHTML();
                }
                return self::getNotFound($mark);
            }
            if ($mark == '/sentmailregistration') {
                if (self::checkMethodPostAndPageName('/index')) {
                    return (new PageCustomerFacadeSentmailregistration($mark, null))->getHTML();
                }
                if (self::checkMethodPostAndPageName('/sentmailregistration')) {
                    return (new PageCustomerFacadeSentmailregistration($mark, null))->getHTML();
                }
                return self::getNotFound($mark);
            }
            if ($mark == '/messagesentmailregistration') {
                if (self::checkMethodPostAndPageName('/sentmailregistration')) {
                    PageCustomerFacadeSentmailregistration::$externalConditionEmailExist = $model->checkEmail($_POST['Email']);
                    $arrRegistrationMessages = PageCustomerFacadeSentmailregistration::getInputNameList();
                    if (self::checkAnalogOr($arrRegistrationMessages) )   {
                        return (new PageCustomerFacadeSentmailregistration($mark, null, $arrRegistrationMessages))->getHTML();
                    }
                    $email_for_registration_email = $_POST['Email'];
                    $lastIdArr = $model->getElements(
                        "SELECT email_for_registration_id FROM email_for_registration ORDER BY email_for_registration_id DESC LIMIT 1",
                        []
                    );
                    if (!$lastIdArr) {
                        $email_for_registration_id = 1;
                    } else {
                        $email_for_registration_id = ++$lastIdArr[0]['email_for_registration_id'];
                    }
                    $email_for_registration_url = '/' . $email_for_registration_id . "_registration";
                    $model->addElements(
                        "INSERT INTO email_for_registration (
                            email_for_registration_id,
                            email_for_registration_email, 
                            email_for_registration_url, 
                            email_for_registration_date_of_link_creation
                        ) VALUES (
                            ?, ?, ?, ?
                        )",
                        [
                            $email_for_registration_id,
                            $email_for_registration_email,
                            $email_for_registration_url,
                            $date
                        ]

                    );
                    $arrPhrases = [
                        'In order to register in our system, you need to follow the link' => [
                            'en' => 'In order to register in our system, you need to follow the link',
                            'ru' => 'Для того, чтобы зарегистрироваться в нашей системе, Вам нужно перейти по ссылке'
                        ],
                        'Registration on the website' => [
                            'en' => 'Registration on the website',
                            'ru' => 'Регистрация на сайте'
                        ],
                        'Chip tuning' => [
                            'en' => 'Chip tuning',
                            'ru' => 'Чип-тюнинг'
                        ],
                        
                    ];
                    $lang = $_POST['lang'];
                    $to = $email_for_registration_email;
                    $headers[] = 'MIME-Version: 1.0';
                    $headers[] = 'Content-type: text/html; charset=iso-8859-1';
                    $subject = self::getText($lang, 'Registration on the website', $arrPhrases);
                    $headers[] = 'From: .' . self::getText($lang, 'Chip tuning', $arrPhrases) . '<' . $GLOBALS['ourMail'] . '>';
                    $message = "
                        <html>
                        <head>
                        <title>" . self::getText($lang, 'Registration on the website', $arrPhrases). "</title>
                        </head>
                        <body>
                        <h1>" . self::getText($lang, 'Registration on the website', $arrPhrases) . "</h1>
                        <p>" . self::getText($lang, 'In order to register in our system, you need to follow ', $arrPhrases) . "<a href='" . $GLOBALS['domain'] . '/' . $email_for_registration_url . "'>the link</a></p>
                        </body>
                        </html>
                        ";
                    if (mail($to, $subject, $message, implode("\r\n", $headers))) {
                        return (new PageCustomerFacadeMessagesentmailregistration($mark, null, $email_for_registration_email))->getHTML();
                    } 
                    else { // заглушка, когда будет работать почта удалить
                        return (new PageCustomerFacadeMessagesentmailregistration($mark, null, $email_for_registration_email))->getHTML(); // заглушка, когда будет работать почта удалить
                    } // заглушка, когда будет работать почта удалить
                }
                return self::getNotFound($mark);
            }
            if ($mark == $email_for_registration_url) {
                $registrationEmail = $model->getElements(
                    "SELECT email_for_registration_email FROM email_for_registration WHERE email_for_registration_id = ?",
                    [$email_for_registration_id]
                )[0]['email_for_registration_email'];
                if ($_SERVER['REQUEST_METHOD'] == "GET") {
                    return (new PageCustomerFacadeRegistration($mark, null, $registrationEmail))->getHTML();
                }
                if (self::checkMethodPostAndPageName('/registration')) {
                    return (new PageCustomerFacadeRegistration($mark, null, $registrationEmail))->getHTML();
                }
                return self::getNotFound($mark);
            }
            if ($mark == '/pay') {
                if (self::checkMethodPostAndPageName('/registration')) {
                    PageCustomerFacadeRegistration::$externalConditionEmailExist = $model->checkEmail($_POST['Email']);
                    $arrRegistrationMessages = PageCustomerFacadeRegistration::getInputNameList();
                    if (self::checkAnalogOr($arrRegistrationMessages) )   {
                        return (new PageCustomerFacadeRegistration($mark, null, null, $arrRegistrationMessages))->getHTML();
                    }
                    $lang = $model->cleaningDataForm($_POST['lang']);
                    $tel = $model->cleaningDataForm($_POST['Tel']);
                    $email = $model->cleaningDataForm($_POST['Email']);
                    $valuta = $model->cleaningDataForm($_POST['Valuta']);
                    $pass = $model->cleaningDataForm($_POST['Pass']);
                    $model->addCustomer($tel, $email, $valuta, $pass, $lang);
                    return self::returnPageCustomerCabinetPayWithStaingId($mark, $email, $pass, $model, $cookiesmanagement);
                } 
                if (self::checkMethodPostAndPageName('/pay') && self::checkMethodName('coins', 'POST', $_POST)) {
                    $coins = (float) $model->cleaningDataForm($_POST['coins']);
                    self::startSessionWithCheck($cookiesmanagement);
                    $model->updateCoins($coins, $_SESSION['customer_id'], 'putOnCoinAccount');
                    return self::returnPageCustomerCabinetPayWithoutStaingId($mark, $model);
                }
                if (self::checkMethodPostAndPageName('/index')) {
                    PageCustomerFacadeIndex::$externalConditionEmailNotExist = !$model->checkEmail($_POST['Email']);
                    PageCustomerFacadeIndex::$externalConditionPassWrong = !$model->checkPass($_POST['Email'], $_POST['Pass']);
                    $arrIndexMessages = PageCustomerFacadeIndex::getInputNameList();
                    if (self::checkAnalogOr($arrIndexMessages)) {
                        return (new PageCustomerFacadeIndex($mark, null, $arrIndexMessages))->getHTML();
                    }
                    return self::returnPageCustomerCabinetPayWithStaingId($mark, $_POST['Email'], $_POST['Pass'], $model, $cookiesmanagement);
                }
                $fromPageArr = ['/dealsdeals', '/dealsdeal', '/treatment', '/bigfile', '/profile', '/profilenotupdated', '/profileupdated', '/history', '/pay' ];
                return self::routingSimplePayPage($fromPageArr, $cookiesmanagement, $model);
            }
            if ($mark == '/treatment') {
                $fromPageArr = ['/dealsdeals', '/dealsdeal', '/history', '/profile', '/profilenotupdated', '/profileupdated', '/pay', '/brand'];
                if (self::checkMethodPostAndPageNames($fromPageArr)) {
                    return self::getTreatmentPage($model, $cookiesmanagement, $mark, false);
                }
            }
            if ($mark == '/brand') {
                if (self::checkMethodPostAndPageName('/treatment')) {
                    if (array_key_exists('vehicle_type', $_POST) && $_POST['vehicle_type'] != null) {
                        return self::getBrandPage($model, $cookiesmanagement, $mark, false);
                    }
                    return self::getTreatmentPage($model, $cookiesmanagement, $mark, true);
                }
                if (self::checkMethodPostAndPageName('/model')) {
                    return self::getBrandPage($model, $cookiesmanagement, $mark, false);
                }
            }
            if ($mark == '/model') {
                if (self::checkMethodPostAndPageName('/brand')) {
                    if (array_key_exists('vehicle_brand', $_POST) && $_POST['vehicle_brand'] != null) {
                        return self::getModelPage($model, $cookiesmanagement, $mark, false);
                    }
                    return self::getBrandPage($model, $cookiesmanagement, $mark, true);
                }
                if (self::checkMethodPostAndPageName('/ecu')) {
                    return self::getModelPage($model, $cookiesmanagement, $mark, false);
                }
            }
            if ($mark == '/ecu') {
                if (self::checkMethodPostAndPageName('/model')) {
                    if (array_key_exists('vehicle_model', $_POST) && $_POST['vehicle_model'] != null) {
                        return self::getEcuPage($model, $cookiesmanagement, $mark, false);
                    }
                    return self::getModelPage($model, $cookiesmanagement, $mark, true);
                }
                if (self::checkMethodPostAndPageName('/allparameters')) {
                    return self::getEcuPage($model, $cookiesmanagement, $mark, false);
                }
            }
            if ($mark == '/allparameters') {
                if (self::checkMethodPostAndPageName('/ecu')) {
                    if (array_key_exists('ecu', $_POST) && $_POST['ecu'] != null) {
                        return self::getAllparametersPage($model, $cookiesmanagement, $mark, false, false, false);
                    }
                    return self::getEcuPage($model, $cookiesmanagement, $mark, true);
                }
                if (self::checkMethodPostAndPageName('/allparameters')) {
                    $service_type_name = 'file treatment';
                    $order_status = 'unpaid';
                    $service_type_id = $model->getElements(
                        "SELECT service_type_id FROM service_type WHERE service_type_name = ?",
                        [$service_type_name]
                    )[0]['service_type_id'];

                    if (!array_key_exists('total_sum', $_POST)) {
                        return self::getNotFound($mark);
                    }
                    $customer_order_amount = $_POST['total_sum'];

                    $checkCustomer_order_amount = $customer_order_amount < 1;

                    $checkConditionOfReadingDevice = !array_key_exists('reading_device', $_POST) || (array_key_exists('reading_device', $_POST) && !$_POST['reading_device']);

                    $checkFileSize = ($_FILES['original_file']['size'] > $GLOBALS['fileSizeFromCustomer']) || ($_FILES['original_file']['error'] == 2);

                    if ($checkCustomer_order_amount || $checkConditionOfReadingDevice || $checkFileSize) {
                        return self::getAllparametersPage($model, $cookiesmanagement, $mark, $checkCustomer_order_amount, $checkConditionOfReadingDevice, $checkFileSize);
                    }
                    self::startSessionWithCheck($cookiesmanagement);
                    $customer_id = $_SESSION['customer_id'];
                    $customer_order_id = $model->addElements(
                        "INSERT INTO customer_order (
                            customer_id, 
                            service_type_id, 
                            customer_order_amount, 
                            customer_order_date, 
                            customer_order_status
                            ) VALUES 
                            (?, ?, ?, ?, ?)",
                        [
                            $customer_id, 
                            $service_type_id, 
                            $model->cleanForm($customer_order_amount), 
                            $date, 
                            $order_status
                        ]
                    );
                    $_SESSION['customer_order_id'] = $customer_order_id;
                    if (array_key_exists('comment', $_POST) && $_POST['comment'] != null) {
                        $message_content = $_POST['comment'];
                        $message_from = 'customer';
                        $message_seen = 0;
                        $model->addElements(
                            "INSERT INTO message (
                                customer_order_id, 
                                message_content, 
                                message_from, 
                                message_date, 
                                message_seen
                                ) VALUES 
                                (?, ?, ?, ?, ?)",
                            [
                                $customer_order_id,
                                $model->cleanForm($message_content),
                                $message_from,
                                $date,
                                $message_seen
                            ]
                        );
                    }
                    
                    $customer_order_data_arr = [
                        'vehicle_type', 
                        'vehicle_brand', 
                        'vehicle_model', 
                        'ecu', 
                        'plate_vehicle', 
                        'vin', 
                        'reading_device'
                    ];
                    foreach ($customer_order_data_arr as $customer_order_data) {
                        $customer_order_data_name = $customer_order_data;
                        if (array_key_exists($customer_order_data_name, $_POST) && $_POST[$customer_order_data_name] != null) {
                            
                            $customer_order_data_value = self::replacesUnderscoreWithSpace($_POST[$customer_order_data_name]);
                            $model->addElements(
                                "INSERT INTO customer_order_data (
                                    customer_order_id, 
                                    customer_order_data_name, 
                                    customer_order_data_value
                                    ) VALUES 
                                    (?, ?, ?)",
                                [
                                    $customer_order_id, 
                                    $model->cleanForm($customer_order_data_name), 
                                    $model->cleanForm($customer_order_data_value) 
                                ]
                            );
                        }
                    }
                    $stringOfServices = $_POST['ServiceSet'];
                    $arrOfServices = explode(', ', $stringOfServices);
                    foreach ($arrOfServices as $service) {
                        $prepearedServiceName = self::replacesSpaceWithUnderscore($service);
                        if (array_key_exists($prepearedServiceName, $_POST) && $_POST[$prepearedServiceName] === 'on') {
                            $model->addElements(
                                "INSERT INTO customer_order_service (customer_order_id, service_name) VALUES (?, ?)",
                                [$customer_order_id, $model->cleanForm($service)]
                            );
                        }
                    }
                    $email = $model->getCustomerEmail($_SESSION['customer_id']);
                    $coins = $model->getCustomerCoins($_SESSION['customer_id']);
                    return self::addFile3($date, $customer_order_id, $mark, $model, $email, $coins);
                }
            }
            if ($mark == '/dealsdeals') {
                $fromPageArr = ['/pay', '/dealsdeal', '/treatment', '/model', '/brand', '/ecu', '/profile', '/profilenotupdated', '/profileupdated', '/history'];
                return self::routingSimpleDealsdeals3($fromPageArr, $cookiesmanagement, $model);
            }
            if ($mark == '/dealsdeal') {
                if (self::checkMethodPostAndPageName('/dealsdeal')) {
                    self::startSessionWithCheck($cookiesmanagement);
                    if (array_key_exists('payService', $_POST)) {
                        $status = $model->getElements(
                            "SELECT customer_order_status FROM customer_order WHERE customer_order_id = ?",
                            [$_SESSION['customer_order_id']]
                        )[0]['customer_order_status'];
                        if ('unpaid' == $status) {
                            $coin_transaction_id = $model->updateCoins((-$_POST['payService']), $_SESSION['customer_id'], 'payDeal');
                            $model->updateElements(
                                "UPDATE customer_order SET customer_order_status = 'paid' WHERE customer_order_id = ?",
                                [$_SESSION['customer_order_id']]
                            );
                            $model->addElements(
                                "INSERT INTO deal_payment (coin_transaction_id, customer_order_id) VALUES (?, ?)",
                                [$coin_transaction_id, $_SESSION['customer_order_id']]
                            );
                        }
                    }
                    if (array_key_exists('comment', $_POST)) {
                        $model->addElements(
                            "INSERT INTO message (
                                customer_order_id,
                                message_content, 
                                message_from, 
                                message_date, 
                                message_seen
                                ) VALUES (?, ?, ?, ?, ?)",
                                [$_SESSION['customer_order_id'], $model->cleanForm($_POST['comment']), 'customer', time(), 0]
                        );
                    }
                    return self::getDealCard3($model, $mark);
                }
                if (self::checkMethodPostAndPageName('/dealsdeals')) {
                    return self::getDealCard3($model, $mark);
                }
            }
            if ($mark == '/history') {
                $fromPageArr = ['/pay', '/dealsdeal', '/treatment', '/profile', '/profilenotupdated', '/profileupdated', '/history', '/bigfile', '/dealsdeals'];
                return self::routingSimpleHistory3($fromPageArr, $cookiesmanagement, $model);
            }
            if ($mark == '/profile') {
                if (self::checkMethodPostAndPageName('/profile')) {
                    $arrProfileInputContent = PageCustomerCabinetProfile::getInputNameList();
                    $arrProfileMessage = [];
                    foreach (PageCustomerCabinetProfile::$arrMessageNames as $prefix => $postfixList) {
                        foreach ($postfixList as $postfix) {
                            if (($postfix == 'Empty') &&  !$arrProfileInputContent[$prefix . $postfix]) {
                                foreach ($postfixList as $postfix) {
                                    $nameElem = $prefix . $postfix;
                                    $arrProfileMessage[$nameElem] =  $arrProfileInputContent[$nameElem];
                                }
                            }
                        }
                    }
                    if (self::checkAnalogOr($arrProfileMessage) )   {
                        return self::getProfile($mark, $cookiesmanagement, $model, $arrProfileMessage);
                    }
                    self::startSessionWithCheck($cookiesmanagement);
                    $updatingParameters = [
                        'Lang' => 'customer_language', 
                        'Tel' => 'customer_telephone',
                        'Valuta' => 'customer_valuta'
                    ];
                    foreach ($updatingParameters as $parameterName => $bdParameterName) {
                        if (array_key_exists($parameterName, $_POST) && $_POST[$parameterName] != '') {
                            $model->updateElements(
                                "UPDATE customer SET $bdParameterName = ? WHERE customer_id = ?",
                                [$_POST[$parameterName], $_SESSION['customer_id']]
                            );
                        }
                    }
                    return self::getProfile($mark, $cookiesmanagement, $model);
                }
                $fromPageArr = ['/pay', '/dealsdeal', '/treatment', '/profilenotupdated', '/profileupdated', '/history', '/bigfile', '/dealsdeals'];
                return self::routingSimpleProfile3($fromPageArr, $cookiesmanagement, $model);
            }
            if ($mark == '/rememberpassword') {
                if (self::checkMethodPostAndPageName('/index')) {
                    return (new PageCustomerFacadeRememberpassword($mark, null))->getHTML();
                }
                if (self::checkMethodPostAndPageName('/rememberpassword')) {
                    return (new PageCustomerFacadeRememberpassword($mark, null))->getHTML();
                }
            }
            if ($mark == '/sentmail') {
                if (self::checkMethodPostAndPageName('/rememberpassword')) {
                    $arrRegistrationMessages = PageCustomerFacadeRememberpassword::getInputNameList();
                    if (self::checkAnalogOr($arrRegistrationMessages) )   {
                        return (new PageCustomerFacadeRememberpassword($mark, null, $arrRegistrationMessages))->getHTML();
                    }
                    $arrPhrases = [
                        'We cannot provide you with a link to reset your password, since the email you specified is not registered in our service' => [
                            'en' => 'We cannot provide you with a link to reset your password, since the email you specified is not registered in our service',
                            'ru' => 'Мы не можем предоставить Вам ссылку на восстановление пароля, так как указанная Вами электронная почта не зарегистрирована в нашем сервисе'
                        ],
                        'Password recovery' => [
                            'en' => 'Password recovery',
                            'ru' => 'Восстаноление пароля'
                        ],
                        'Chip tuning' => [
                            'en' => 'Chip tuning',
                            'ru' => 'Чип-тюнинг'
                        ],
                        "In order to recover your password, you need to follow" => [
                            'en' => 'In order to recover your password, you need to follow',
                            'ru' => 'Для того, чтобы восстановить пароль, Вам нужно перейти'
                        ],
                        'the link' => [
                            'en' => 'the link',
                            'ru' => 'ссылка'
                        ]
                    ];
                    $isExistEmail = $model->checkEmail($_POST['Email']);
                    $lang = $_POST['lang'];
                    $to = $_POST['Email'];
                    $headers[] = 'MIME-Version: 1.0';
                    $headers[] = 'Content-type: text/html; charset=iso-8859-1';
                    $subject = self::getText($lang, 'Password recovery', $arrPhrases);
                    $headers[] = 'From: .' . self::getText($lang, 'Chip tuning', $arrPhrases) . '<' . $GLOBALS['ourMail'] . '>';
                    if (!$isExistEmail) {
                        $message = "
                        <html>
                        <head>
                        <title>" . self::getText($lang, 'Password recovery', $arrPhrases). "</title>
                        </head>
                        <body>
                        <h1>" . self::getText($lang, 'Password recovery', $arrPhrases) . "</h1>
                        <p>" . self::getText($lang, 'We cannot provide you with a link to reset your password, since the email you specified is not registered in our service', $arrPhrases) . "</p>
                        </body>
                        </html>
                        ";
                    } else {
                        $customer_id = $model->getElements(
                            "SELECT customer_id FROM customer WHERE customer_email = ?",
                            [$_POST['Email']]
                        )[0]['customer_id'];
                        $customer_password_recovery_url = '/' . $date . '_' . $customer_id;
                        $url = $GLOBALS['domain'] . $customer_password_recovery_url;
                        
                        $customer_password_recovery_id = $model->addElements(
                            "INSERT INTO customer_password_recovery (
                                customer_password_recovery_url,
                                customer_password_recovery_date_of_link_creation,
                                customer_id
                                ) VALUES (?, ?, ?)", 
                            [$customer_password_recovery_url,  $date, $customer_id]
                        );
                        $message = "
                        <html>
                        <head>
                        <title>" . self::getText($lang, 'Password recovery', $arrPhrases) . "</title>
                        </head>
                        <body>
                        <h1>" . self::getText($lang, 'Password recovery', $arrPhrases) . "</h1>
                        <p>" . self::getText($lang, 'In order to recover your password, you need to follow', $arrPhrases) . "<a href='$url'>" . self::getText($lang, 'the link', $arrPhrases) . "</a></p>
                        </body>
                        </html>
                        ";
                    }
                    if (mail($to, $subject, $message, implode("\r\n", $headers))) {
                        return (new PageCustomerFacadeSentmail($mark, null))->getHTML();
                    } 
                    else { // заглушка, когда будет работать почта удалить
                        return (new PageCustomerFacadeSentmail($mark, null))->getHTML(); // заглушка, когда будет работать почта удалить
                    } // заглушка, когда будет работать почта удалить
                }
                if (self::checkMethodPostAndPageName('/sentmail')) {
                    return (new PageCustomerFacadeSentmail($mark, null))->getHTML();
                }
            }
            // if ($mark == '/sentmailregistration') {
            //     if (self::checkMethodPostAndPageName('/index')) {
            //         return (new PageCustomerFacadeSentmailregistration($mark, null))->getHTML();
            //     }
            //     if (self::checkMethodPostAndPageName('/sentmailregistration')) {
            //         return (new PageCustomerFacadeSentmailregistration($mark, null))->getHTML();
            //     }
            //     return self::getNotFound($mark);
            // }
            if ($mark == $customerPasswordRecoveryUrl) {
                if ($mark == '/wronglink') {
                    if ($_SERVER['REQUEST_METHOD'] == "GET") {
                        return 'Ваша ссылка либо уже использована, либо просрочена';
                    }
                    if (self::checkMethodPostAndPageName('/wronglink')) {
                        return 'Ваша ссылка либо уже использована, либо просрочена';
                    }
                    return self::getNotFound($mark);
                }
                if ($customerPasswordRecoveryId) {
                    $customer_id = $model->getElements(
                        "SELECT customer_id FROM customer_password_recovery WHERE customer_password_recovery_id = ?",
                        [$customerPasswordRecoveryId]
                    )[0]['customer_id'];
                    if ($_SERVER['REQUEST_METHOD'] == "GET") {
                        $model->updateElements(
                            "UPDATE customer_password_recovery SET customer_password_recovery_date_of_visit_link = ? WHERE customer_password_recovery_id = ?",
                            [$date, $customerPasswordRecoveryId]
                        );
                        return (new PageCustomerFacadeNewpassword($mark, $customer_id))->getHTML();
                    }
                    if (self::checkMethodPostAndPageName('/newpassword')) {
                        $showMessageList = PageCustomerFacadeNewpassword::getInputNameList();
                        $customer_email = $model->getElements(
                            "SELECT customer_email FROM customer WHERE customer_id = ?",
                            [$customer_id]
                        )[0]['customer_email'];
                        if (self::checkAnalogOr($showMessageList)) {
                            return (new PageCustomerFacadeNewpassword($mark, $customer_id, $customer_email, $showMessageList))->getHTML();
                        }
                        $pass = $model->cleaningDataForm($_POST['Pass']);
                        
                        $model->updatePass($pass, $customer_id);
                        $model->updateElements(
                            "UPDATE customer_password_recovery SET customer_password_recovery_date_password = ?, customer_password_recovery_notwork = ? WHERE customer_password_recovery_id = ?",
                            [$date, 1, $customerPasswordRecoveryId]
                        );
                        return self::returnPageCustomerCabinetPayWithStaingId($mark, $customer_email, $pass, $model, $cookiesmanagement);
                    }
                }
            }
            if ($_SERVER['REQUEST_URI'] == '/maingate') {
                if (self::checkMethodPostAndPageNameAndReferer('/maingate', $checkReferer)) {
                    //return '"maingage Page" must be here'; //Это нужно было для проверки, можно удалять
                }
                if ($_SERVER['REQUEST_METHOD'] == "GET") {

                    //return "<form method='POST' action=''/maingate''><input type='hidden' value='/maingate' name='Page'><input type='submit'></form>"; //это нужно было для проверки можно удалять
                    
                }
                return self::getNotFound($mark);
            }

            
            
            return self::getNotFound($mark);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    static function printPage()
    {
        echo self::getPage();
    }
}