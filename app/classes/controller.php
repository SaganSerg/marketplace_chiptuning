<?php
abstract class Controller
{
    static private $pppp = '123456';

    static private function checkAnalogOr(array $arr)
    {
        return !!array_search(true, $arr);
    }
    static private function checkAnalogAnd(Array $arr)
    {
        return !!array_search(false, $arr);
    }
    static private function checkExistPosts($arr)
    {
        foreach ($arr as $elem) {
            if (!array_key_exists($elem, $_POST)) {
                return false;
            }
        }
        return true;
    }


    static private function returnPageCustomerCabinetPayWithStaingId($receiveBD, $mark, $email, $pass, Model $model, $cookiesmanagement)
    {
        $id = $model->getCustomerElementByEmailPass($email, $pass, 'customer_id');
        $cookiesmanagement->customerSessionStart();
        $_SESSION['customer_id'] = $id;
        return self::returnPageCustomerCabinetPayWithoutStaingId($receiveBD, $mark, $model);
    }


    static private function returnPageCustomerCabinetPayWithoutStaingId($receiveBD, $mark, Model $model)
    {
        $coins = $model->getCustomerElementById($receiveBD, 'customer_coins', $_SESSION['customer_id']);
        $email = $model->getCustomerElementById($receiveBD, 'customer_email', $_SESSION['customer_id']);
        return (new PageCustomerCabinetPay($mark, $_SESSION['customer_id'], $email, $coins))->getHTML();
    }


    static private function getDealCard(PDO $receiveBD, Model $model, string $mark)
    {
        if (session_status() !== 2) {
            $GLOBALS['cookiesmanagement']->customerSessionStart();
        }
        $coins = $model->getCustomerElementById($receiveBD, 'customer_coins', $_SESSION['customer_id']);
        $login = $model->getCustomerElementById($receiveBD, 'customer_login', $_SESSION['customer_id']);
        if (array_key_exists('order_id', $_POST)) {
            $_SESSION['order_id'] = $_POST['order_id'];
        }
        $idItemList = $model->getOrderItemIdByOrderId($receiveBD, $_SESSION['order_id']);
        $dealSpecification = $model->getTimeIdStatusAmountOrdersByIdOrder($receiveBD, $_SESSION['order_id']);
        $parametersServicesMessages = [];
        foreach ($idItemList as $idItem) {
            $parametersServicesMessages[$idItem]['parameter_list'] = $model->getAllFileprocessingByIdOrderitem($receiveBD, $idItem);
            switch($parametersServicesMessages[$idItem]['parameter_list']['file_processing_vehicle_type']) {
                case "Car": $parametersServicesMessages[$idItem]['service_list'] = $model->getAllFileprocessingCarByIdOrderitem($receiveBD, $idItem);
                break;
                // сюда нужно будет дополнительно пописать другие условия для других транспортных средств
            }
            $parametersServicesMessages[$idItem]['message_list'] = $model->getAllMessagesByIdOrderitem($receiveBD, $idItem);
            foreach($parametersServicesMessages[$idItem]['message_list'] as $elemArr) {
                if ($elemArr['message_from'] == 'provider' && $elemArr['message_seen'] == 0) {
                    $model->updateElementByUniqueParameter($receiveBD,  'messages', 'message_seen', 1, 'message_id', $elemArr['message_id']);
                }
            }
        }
        return (new PageCustomerCabinetDealsdeal($mark, $_SESSION['customer_id'], $login, $coins, $dealSpecification, $parametersServicesMessages))->getHTML();
    }


    static private function addFile(int $date, string $order_id, string $order_item_id, string $mark, Model $model, PDO $receiveBD, string $login, string $coins)
    {
        $day = date('Y-m-d', $date);
        $dirPathWithoutRootPath = $GLOBALS['incomingFileDir'] . '/' . $_SESSION['customer_id'] . '/' . $order_id . '/' . $order_item_id . '/' . $day;
        $dirPath = $GLOBALS['saveFilePath'] . $dirPathWithoutRootPath;
        if (!is_dir($dirPath)) {
            if (!mkdir($dirPath, 0777, true)) {
                return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
            }
        }
        $file_extension = strrchr($_FILES['original_file']['name'], '.');
        $path_to_file = $dirPath . '/' . $order_item_id . '_' . $date . $file_extension;
        $path_to_file_WithoutRootPath = $dirPathWithoutRootPath . '/' . $order_item_id . '_' . $date . $file_extension;
        if ($_FILES['original_file']['size'] < $GLOBALS['fileSizeFromCustomer']) {
            if (move_uploaded_file($_FILES['original_file']['tmp_name'],  $path_to_file)) {
                $open_resurs = fopen($path_to_file, 'rb');
                $file_is_string = fread($open_resurs, $GLOBALS['fileSizeFromCustomer']);
                if (customCRC16($file_is_string) == $_POST['checksum']) {
                    $model->updateElementByUniqueParameter($receiveBD,  'file_processing', 'file_processing_path_to_file', $path_to_file_WithoutRootPath, 'order_item_id', $order_item_id);
                    return self::getDealCard($receiveBD, $model, $mark);
                } else {
                    if (unlink($path_to_file)) { // данный участок не работает
                        rmdir($dirPath);
                    }
                    return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
                }
            } else {
                $errorCode = $_FILES['original_file']['error'];
                if ($errorCode === 1 || $errorCode === 2) {
                    return (new PageCustomerCabinetBigfile($mark, $_SESSION['customer_id'], $login, $coins))->getHTML();
                }
                return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
            }
        } else {
            return (new PageCustomerCabinetBigfile($mark, $_SESSION['customer_id'], $login, $coins))->getHTML();
        }
    }

    // это новая функция добавления файла

    static private function addFile3 (int $date, int $customer_order_id, string $mark, Model $model, string $email, string $coins )
    {
        $time = date('Y-m-d-H-i-s', $date);
        $dirPathWithoutRootPath = $GLOBALS['incomingFileDir'] . '/' . $_SESSION['customer_id'] . '/' . $customer_order_id . '/' . $time;
        $dirPath = $GLOBALS['saveFilePath'] . $dirPathWithoutRootPath;
        if (!is_dir($dirPath)) {
            if (!mkdir($dirPath, 0777, true)) {
                return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
            }
        }
        $path_to_file = $dirPath . '/' . $_FILES['original_file']['name'];
        $path_to_file_WithoutRootPath = $dirPathWithoutRootPath . '/' . $_FILES['original_file'];
        if ($_FILES['original_file']['size'] < $GLOBALS['fileSizeFromCustomer']) {
            if (move_uploaded_file($_FILES['original_file']['tmp_name'], $path_to_file)) {
                $open_resurs = fopen($path_to_file, 'rb');
                $file_is_string = fread($open_resurs, $GLOBALS['fileSizeFromCustomer']);
                if (customCRC16($file_is_string) == $_POST['checksum']) {
                    $model->addElements(
                        "INSERT INTO file_path (
                            customer_order_id, 
                            file_path_date, 
                            file_path_path, 
                            file_path_what_file
                            ) VALUES (?, ?, ?, ?)",
                        [
                            $customer_order_id,
                            $data, 
                            $path_to_file_WithoutRootPath,
                            'from customer'
                        ]
                    );
                    return self::getDealCard($receiveBD, $model, $mark); // это нужно будет передалать 
                } else {
                    // надо будет написать скрипт удаляющий ненужные папки
                    return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
                }
            } else {
                $errorCode = $_FILES['original_file']['error'];
                if ($errorCode === 1 || $errorCode === 2) {
                    return (new PageCustomerCabinetBigfile($mark, $_SESSION['customer_id'], $login, $coins))->getHTML(); // надо будет переделать
                }
                return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
            }
        }
    }

    // это конец функции добавления файла

    static function routingSimplePage(array $fromPageArr, string $toPage) // этот метод для страниц 'Notfound', 'About', 'Contacts', 'Termsuse'
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && array_key_exists('Page', $_POST)) {
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    
                    $class = 'PageCustomerFacade' . $toPage;
                    return (new $class($toPage, null))->getHTML();
                }
            }
        }
        return (new PageCustomerFacadeNotfound($toPage, null))->getHTML();
    }


    static function routingSimpleCabinetPage(array $fromPageArr, string $toPage, Model $model, $receiveBD)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && array_key_exists('Page', $_POST)) {
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    $class = 'PageCustomerCabinet' . $toPage;
                    $GLOBALS['cookiesmanagement']->customerSessionStart();
                    $coins = $model->getCustomerElementById($receiveBD, 'customer_coins', $_SESSION['customer_id']);
                    $login = $model->getCustomerElementById($receiveBD, 'customer_login', $_SESSION['customer_id']);
                    return (new $class($toPage, $_SESSION['customer_id'], $login, $coins))->getHTML();
                }
            }
        }
        return (new PageCustomerFacadeNotfound($toPage, null))->getHTML();
    }

    static function routingSimplePayPage(array $fromPageArr, $cookiesmanagement, $model, $receiveBD)
    {
        $toPage = "Pay";
        if ($_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists('Page', $_POST)) {
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    $cookiesmanagement->customerSessionStart();
                    return self::returnPageCustomerCabinetPayWithoutStaingId($receiveBD, $toPage, $model);
                }
            }
        }
        return (new PageCustomerFacadeNotfound($toPage, null))->getHTML();
    }

    static function routingSimpleDealsdeals(array $fromPageArr, $cookiesmanagement, $model, $receiveBD)
    {
        $toPage = 'Dealsdeals';
        if ($_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists('Page', $_POST)) {
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    $cookiesmanagement->customerSessionStart();
                    unset($_SESSION['order_item_id']);
                    unset($_SESSION['order_id']);
                    $coins = $model->getCustomerElementById($receiveBD, 'customer_coins', $_SESSION['customer_id']);
                    $login = $model->getCustomerElementById($receiveBD, 'customer_login', $_SESSION['customer_id']);
                    $attributes = $model->getTimeIdStatusAmountOrdersByIdCustomer($receiveBD, $_SESSION['customer_id']);
                    return (new PageCustomerCabinetDealsdeals($toPage, $_SESSION['customer_id'], $login, $coins, $attributes))->getHTML();
                }
            }
        }
        return (new PageCustomerFacadeNotfound($toPage, null))->getHTML();
    }


    static function routingSimpleHistory(array $fromPageArr, $cookiesmanagement, $model, $receiveBD)
    {
        $toPage = 'History';
        if ($_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists('Page', $_POST)) {
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    $cookiesmanagement->customerSessionStart();
                    unset($_SESSION['order_item_id']);
                    unset($_SESSION['order_id']);
                    $coins = $model->getCustomerElementById($receiveBD, 'customer_coins', $_SESSION['customer_id']);
                    $login = $model->getCustomerElementById($receiveBD, 'customer_login', $_SESSION['customer_id']);
                    $buyList = $model->getElementsByOneParameter($receiveBD, '*', 'buy_coins', 'customer_id', $_SESSION['customer_id']);
                    return (new PageCustomerCabinetHistory($toPage, $_SESSION['customer_id'], $login, $coins, $buyList))->getHTML();
                }
            }
        }
        return (new PageCustomerFacadeNotfound($toPage, null))->getHTML();
    }


    static function routingSimpleProfile(array $fromPageArr, $cookiesmanagement, $model, $receiveBD)
    {
        $toPage = 'Profile';
        if ($_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists('Page', $_POST)) {
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    $cookiesmanagement->customerSessionStart();
                    $coins = $model->getCustomerElementById($receiveBD, 'customer_coins', $_SESSION['customer_id']);
                    $login = $model->getCustomerElementById($receiveBD, 'customer_login', $_SESSION['customer_id']);
                    $tel = $model->getCustomerElementById($receiveBD, 'customer_telephone', $_SESSION['customer_id']);
                    $email = $model->getCustomerElementById($receiveBD, 'customer_email', $_SESSION['customer_id']);
                    $valuta = $model->getCustomerElementById($receiveBD, 'customer_valuta', $_SESSION['customer_id']);
                    $arrProfile = [
                        'Login' => $login,
                        'Tel' => $tel,
                        'Email' => $email,
                        'Valuta' => $valuta
                    ];
                    return (new PageCustomerCabinetProfile($toPage, $_SESSION['customer_id'], $login, $coins, $arrProfile))->getHTML();
                }
            }
        }
        return (new PageCustomerFacadeNotfound($toPage, null))->getHTML();
    }

    static function getAdminPage($receiveBD, $mark, $model)
    {
        $paidDeals = $model->getElementsByOneParameterWithNameArr($receiveBD, 'order_id, customer_id', 'orders', 'order_status', 'paid', 'customer_id');
        $notMessageSeenDeals = $model->getNotSeenMessagesOrderIdFrom($receiveBD, 'customer');
        $beingDoneDeals = $model->getElementsByOneParameterWithNameArr($receiveBD, 'order_id, customer_id', 'orders', 'order_status', 'being_done', 'customer_id');
        return (new PageProviderAdmin($mark, $paidDeals, $notMessageSeenDeals, $beingDoneDeals))->getHTML();
    }

    static function getAdminPageWithSession($receiveBD, $mark, $model)
    {
        session_start();
        if (!array_key_exists('pass', $_SESSION)) {
            return (new PageProviderGate($mark, true))->getHTML();
        }
        if ($_SESSION['pass'] != self::$pppp) {
            return (new PageProviderGate($mark, true))->getHTML();
        }
        return self::getAdminPage($receiveBD, $mark, $model);
    }
    
    static function checkPostMethodValue($name, $value)
    {
        return $_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists($name, $_POST) && $_POST[$name] == $value;
    }
    static function checkMethodPostAndPageName($pageName)
    {
        return self::checkPostMethodValue('Page', $pageName);
    }
    static function getDealPageWithSession ($receiveBD, $mark, $model, $ifAddComment, $downloadFileIdItem)
    {
        if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
            return $gettingPage;
        }
        if ($ifAddComment) {
            $model->addElement($receiveBD, 'messages', [
                'order_item_id' => $_POST['id_item'],
                'message_content' => $_POST['comment'], 
                'message_from' => 'provider',
                'message_date' => time(),
                'message_seen' => 0,
            ]);
        }
        return self::getDealPage($receiveBD, $mark, $model, $downloadFileIdItem);
    }
    static function getDealPage($receiveBD, $mark, $model, $downloadFileIdItem)
    {
        $customerId = $model->getAllOrdersByIdOrder($receiveBD, $_POST['order_id'])['customer_id'];
        $idItemList = $model->getOrderItemIdByOrderId($receiveBD, $_POST['order_id']);
        $dealSpecification = $model->getAllOrdersByIdOrder($receiveBD, $_POST['order_id']);
        $customerParameters = $model->getAllCustomerByIdCustomer($receiveBD, $customerId);
        $parametersServicesMessages = [];
        foreach ($idItemList as $idItem) {
            $parametersServicesMessages[$idItem]['parameter_list'] = $model->getAllFileprocessingByIdOrderitem($receiveBD, $idItem);
            switch($parametersServicesMessages[$idItem]['parameter_list']['file_processing_vehicle_type']) {
            case "Car": $parametersServicesMessages[$idItem]['service_list'] = $model->getAllFileprocessingCarByIdOrderitem($receiveBD, $idItem);
            break;
            // сюда нужно будет дополнительно пописать другие условия для других транспортных средств
            }
            $parametersServicesMessages[$idItem]['message_list'] = $model->getAllMessagesByIdOrderitem($receiveBD, $idItem);
        }
        return (new PageProviderDeal($mark, $customerParameters, $dealSpecification, $parametersServicesMessages, $downloadFileIdItem))->getHTML();
    }
    static function uploadFile($mark, $receiveBD, $model, $idItem, $postfixPath)
    {
        $lastPartPath = $model->getElementByUniqueParameter($receiveBD, 'file_processing_path_to_' . $postfixPath, 'file_processing', 'order_item_id', $idItem);
        if (!$lastPartPath) {
            return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
        }

        $path_to_file = $GLOBALS['saveFilePath'] . $lastPartPath;

        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($path_to_file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path_to_file));

        $filename = $path_to_file;
        $handle = fopen($filename, "rb");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        return $contents;
    }
    static function startSessionAndCheckPassForProv($mark)
    {
        session_start();
        if (!array_key_exists('pass', $_SESSION)) {
            return (new PageProviderGate($mark, true))->getHTML();
        }
        if ($_SESSION['pass'] != self::$pppp) {
            return (new PageProviderGate($mark, true))->getHTML();
        }
    }
    static function getValutaPage($receiveBD, $mark, $model)
    {
        $valutaValues = $model->getValuta($receiveBD);
        return (new PageProviderValuta($mark, $valutaValues))->getHTML();
    }
    static function getPage()
    {
        try {
            // $mark = ($_SERVER['REQUEST_URI']);
            $mark = 'Notfound';
            switch ($_SERVER['REQUEST_URI']) {
                case '':
                case '/':
                case '/index':
                case '/index.php':
                case '/index.html':
                    $mark = 'Index';
                break;
                case '/pay':
                    $mark = "Pay";
                    break;
                case '/registration':
                    $mark = 'Registration';
                break;
                case '/treatment':
                    $mark = 'Treatment';
                break;
                case '/brand':
                    $mark = 'Brand';
                break;
                case '/model':
                    $mark = 'Model';
                break;
                case '/ecu':
                    $mark = 'Ecu';
                break;
                case '/allparameters':
                    $mark = 'Allparameters';
                break;
                case '/dealsdeal':
                    $mark = 'Dealsdeal';
                break;
            }
            $model = new Model();
            $receiveBD = $model->appDB();
            $cookiesmanagement = $GLOBALS['cookiesmanagement'];
            $arrAllCustomerPage = [
                'About',
                'Index',
                'Registration',
                'Pay',
                'Bigfile',
                'Dealsdeals',
                'Dealsdeal',
                'History',
                'Profile',
                'Profilenotupdated',
                'Profileupdated',
                'Treatment',
                'Notfound',
                'Termsuse',
                'Contacts'
            ];
            $arrFacadeCustomerPage = [
                'About',
                'Index',
                'Registration',
                'Notfound',
                'Termsuse',
                'Contacts'
            ];
            if ($mark == 'Index') {
                if ($_SERVER['REQUEST_METHOD'] == "GET" || $_SERVER['REQUEST_METHOD'] == "POST") {
                    return (new PageCustomerFacadeIndex($mark, null))->getHTML();
                }
                return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
            }
            if ($mark == 'Pay') {
                if (self::checkMethodPostAndPageName('Registration')) {
                    PageCustomerFacadeRegistration::$externalConditionEmailExist = $model->checkEmail($_POST['Email']);
                    $arrRegistrationMessages = PageCustomerFacadeRegistration::getInputNameList();
                    if (self::checkAnalogOr($arrRegistrationMessages) )   {
                        return (new PageCustomerFacadeRegistration($mark, null, $arrRegistrationMessages))->getHTML();
                    }
                    $lang = $model->cleaningDataForm($_POST['lang']);
                    $tel = $model->cleaningDataForm($_POST['Tel']);
                    $email = $model->cleaningDataForm($_POST['Email']);
                    $valuta = $model->cleaningDataForm($_POST['Valuta']);
                    $pass = $model->cleaningDataForm($_POST['Pass']);
                    $model->addCustomer($tel, $email, $valuta, $pass, $lang);
                    return self::returnPageCustomerCabinetPayWithStaingId($receiveBD, $mark, $email, $pass, $model, $cookiesmanagement);
                } 
                if (self::checkMethodPostAndPageName('Pay')) {
                    $coins = (float) $model->cleaningDataForm($_POST['coins']);
                    $cookiesmanagement->customerSessionStart();
                    $model->updateCoins($coins, $_SESSION['customer_id']);
                    return self::returnPageCustomerCabinetPayWithoutStaingId($receiveBD, $mark, $model);
                }
                if (self::checkMethodPostAndPageName('Index')) {
                    PageCustomerFacadeIndex::$externalConditionEmailNotExist = !$model->checkEmail($_POST['Email']);
                    PageCustomerFacadeIndex::$externalConditionPassWrong = !$model->checkPass($_POST['Email'], $_POST['Pass']);
                    $arrIndexMessages = PageCustomerFacadeIndex::getInputNameList();
                    if (self::checkAnalogOr($arrIndexMessages)) {
                        return (new PageCustomerFacadeIndex($mark, null, $arrIndexMessages))->getHTML();
                    }
                    return self::returnPageCustomerCabinetPayWithStaingId($receiveBD, $mark, $_POST['Email'], $_POST['Pass'], $model, $cookiesmanagement);
                }
                $fromPageArr = ['Dealsdeals', 'Dealsdeal', 'Treatment', 'Bigfile', 'Profile', 'Profilenotupdated', 'Profileupdated', 'History' ];
                return self::routingSimplePayPage($fromPageArr, $cookiesmanagement, $model, $receiveBD);
            }
            if ($mark == "Registration") {
                if (self::checkMethodPostAndPageName('Index')) {
                    return (new PageCustomerFacadeIndex($mark, null))->getHTML();
                }
                if (self::checkMethodPostAndPageName('Registration')) {
                    return (new PageCustomerFacadeRegistration($mark, null))->getHTML();
                }
                return (new PageCustomerFacadeIndex($mark, null))->getHTML();
            }
            if ($mark == 'Treatment') {
                if (self::checkMethodPostAndPageName('Treatment')) {
                    $order_status = 'unpaid';
                    $date = time();
                    $servicesArr = PageCustomerCabinetTreatment::$listServices[$_POST['vehicle_type']];
                    $parameterVehicleTypeArr = [];
                    foreach($servicesArr as $service) {
                        $service = strtolower($service);
                        $serviceValue = array_key_exists($service, $_POST) ? $_POST[$service] : 0;
                        $parameterVehicleTypeArr['file_processing_' . strtolower($_POST['vehicle_type']) . '_' . $service] = $serviceValue;
                    }
                    $processed_comment = nl2br(htmlspecialchars($_POST['comment'])); 
                    $userType =  'customer';
                    $message_seen = 0;
                    $fileAddress = 'no_file'; // это строка будет хранится в поле адреса, пока файл не пришел
                    $cookiesmanagement->customerSessionStart();
                    $coins = $model->getCustomerElementById($receiveBD, 'customer_coins', $_SESSION['customer_id']);
                    $login = $model->getCustomerElementById($receiveBD, 'customer_login', $_SESSION['customer_id']);
                    $idList = $model->addOrder(
                        $receiveBD,
                        $_SESSION['customer_id'],
                        $_POST['total_sum'],
                        $date,
                        $order_status,
                        $processed_comment,
                        $userType,
                        $message_seen,
                        $_POST['vehicle_type'],
                        $_POST['vehicle_brand'],
                        $_POST['vehicle_model'],
                        $_POST['vehicle_details'],
                        $_POST['plate_vehicle'],
                        $_POST['vin_vehicle_identification_number'],
                        $_POST['reading_device'],
                        $fileAddress,
                        $parameterVehicleTypeArr
                    );
                    $order_item_id = $idList['order_item_id'];
                    $order_id = $idList['order_id'];
                    $_SESSION['order_item_id'] = $order_item_id;
                    $_SESSION['order_id'] = $order_id;
                    return self::addFile($date, $order_id, $order_item_id, $mark, $model, $receiveBD, $login, $coins);
                }
                if (self::checkMethodPostAndPageName('Bigfile')) {
                    $cookiesmanagement->customerSessionStart();
                    $coins = $model->getCustomerElementById($receiveBD, 'customer_coins', $_SESSION['customer_id']);
                    $login = $model->getCustomerElementById($receiveBD, 'customer_login', $_SESSION['customer_id']);
                    $order_item_id = $_SESSION['order_item_id'];
                    $order_id = $_SESSION['order_id'];
                    $contentType = explode(';', apache_request_headers()['Content-Type'])[0];
                    if ('multipart/form-data' == $contentType) {
                        $date = time();
                        return self::addFile($date, $order_id, $order_item_id, $mark, $model, $receiveBD, $login, $coins);
                    }
                    return (new PageCustomerCabinetTreatment($mark, $_SESSION['customer_id'], $login, $coins))->getHTML();
                }
                $fromPageArr = ['Dealsdeals', 'Dealsdeal', 'History', 'Profile', 'Profilenotupdated', 'Profileupdated', 'Pay'];
                return self::routingSimpleCabinetPage($fromPageArr, $mark, $model, $receiveBD);
            }
            if ($mark == 'Treatment') {
                if (self::checkMethodPostAndPageName('Pay')) {
                    $conditionIdList = $model->getElements(
                        'SELECT condition_id_id FROM condition_id WHERE condition_id_works = 1',
                        []
                    );
                    $serviceTypeName = 'file treatment';
                    $dataName = 'vehicle type';
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
                    $cookiesmanagement->customerSessionStart();
                    $email = $model->getElements(
                        "SELECT customer_email FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_email'];
                    $coins = $model->getElements(
                        "SELECT customer_coins FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_coins'];
                    return (new PageCustomerCabinetTreatment($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr))->getHTML();
                }
            }
            if ($mark == 'Brand') {
                if (self::checkMethodPostAndPageName('Treatment')) {
                    if (array_key_exists('vehicle_type', $_POST) && $_POST['vehicle_type'] != null) {
                        // $conditionIdList = $model->getElements(
                        //     'SELECT condition_id_id FROM condition_id WHERE condition_id_works = 1',
                        //     []
                        // );
                        $conditionIdList = $model->getElements(
                            "SELECT condition_id_id FROM condition_value WHERE data_name = 'vehicle type' AND data_value = ?",
                            [$_POST['vehicle_type']]
                        );
                        // SELECT condition_id_id FROM condition_value WHERE data_name = 'vehicle type' AND data_value = 'car'
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
                        $cookiesmanagement->customerSessionStart();
                        $email = $model->getElements(
                            "SELECT customer_email FROM customer WHERE customer_id = ?",
                            [$_SESSION['customer_id']]
                        )[0]['customer_email'];
                        $coins = $model->getElements(
                            "SELECT customer_coins FROM customer WHERE customer_id = ?",
                            [$_SESSION['customer_id']]
                        )[0]['customer_coins'];
                        return (new PageCustomerCabinetBrand($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr, $_POST['vehicle_type']))->getHTML();
                    }
                    $conditionIdList = $model->getElements(
                        'SELECT condition_id_id FROM condition_id WHERE condition_id_works = 1',
                        []
                    );
                    $serviceTypeName = 'file treatment';
                    $dataName = 'vehicle type';
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
                    $cookiesmanagement->customerSessionStart();
                    $email = $model->getElements(
                        "SELECT customer_email FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_email'];
                    $coins = $model->getElements(
                        "SELECT customer_coins FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_coins'];
                    return (new PageCustomerCabinetTreatment($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr, true))->getHTML();
                }
            }
            if ($mark == 'Model') {
                if (self::checkMethodPostAndPageName('Brand')) {
                    if (array_key_exists('vehicle_brand', $_POST) && $_POST['vehicle_brand'] != null) {
                        // $conditionIdList = $model->getElements(
                        //     'SELECT condition_id_id FROM condition_id WHERE condition_id_works = 1',
                        //     []
                        // );
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
                        $cookiesmanagement->customerSessionStart();
                        $email = $model->getElements(
                            "SELECT customer_email FROM customer WHERE customer_id = ?",
                            [$_SESSION['customer_id']]
                        )[0]['customer_email'];
                        $coins = $model->getElements(
                            "SELECT customer_coins FROM customer WHERE customer_id = ?",
                            [$_SESSION['customer_id']]
                        )[0]['customer_coins'];
                        return (new PageCustomerCabinetModel($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr, $_POST['vehicle_type'], $_POST['vehicle_brand']))->getHTML();
                    }
                    $conditionIdList = $model->getElements(
                        "SELECT condition_id_id FROM condition_value WHERE data_name = 'vehicle type' AND data_value = ?",
                        [$_POST['vehicle_type']]
                    );
                    // SELECT condition_id_id FROM condition_value WHERE data_name = 'vehicle type' AND data_value = 'car'
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
                    $cookiesmanagement->customerSessionStart();
                    $email = $model->getElements(
                        "SELECT customer_email FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_email'];
                    $coins = $model->getElements(
                        "SELECT customer_coins FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_coins'];
                    return (new PageCustomerCabinetBrand($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr, $_POST['vehicle_type']))->getHTML();
                }
            }
            if ($mark == 'Ecu') {
                if (self::checkMethodPostAndPageName('Model')) {
                    if (array_key_exists('vehicle_model', $_POST) && $_POST['vehicle_model'] != null) {
                        // $conditionIdList = $model->getElements(
                        //     'SELECT condition_id_id FROM condition_id WHERE condition_id_works = 1',
                        //     []
                        // );
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
                        $cookiesmanagement->customerSessionStart();
                        $email = $model->getElements(
                            "SELECT customer_email FROM customer WHERE customer_id = ?",
                            [$_SESSION['customer_id']]
                        )[0]['customer_email'];
                        $coins = $model->getElements(
                            "SELECT customer_coins FROM customer WHERE customer_id = ?",
                            [$_SESSION['customer_id']]
                        )[0]['customer_coins'];
                        return (new PageCustomerCabinetEcu($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr, $_POST['vehicle_type'], $_POST['vehicle_brand'], $_POST['vehicle_model']))->getHTML();
                    }
                    // $conditionIdList = $model->getElements(
                    //     'SELECT condition_id_id FROM condition_id WHERE condition_id_works = 1',
                    //     []
                    // );
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
                    $cookiesmanagement->customerSessionStart();
                    $email = $model->getElements(
                        "SELECT customer_email FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_email'];
                    $coins = $model->getElements(
                        "SELECT customer_coins FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_coins'];
                    return (new PageCustomerCabinetModel($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr, $_POST['vehicle_type'], $_POST['vehicle_brand'], true))->getHTML();
                }
            }
            if ($mark == 'Allparameters') {
                if (self::checkMethodPostAndPageName('Ecu')) {
                    if (array_key_exists('ecu', $_POST) && $_POST['ecu'] != null) {
                        $conditioinIdWorks = $model->getOneElementArr(
                            "SELECT condition_id_id FROM condition_id WHERE condition_id_works",
                            [$_POST['vehicle_model']], 'condition_id_id'
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
                            return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
                        }
                        $conditioinIdId = [];
                        foreach ($conditioinIdIdRaw as $conditioinIdIdIndex) {
                            $conditioinIdId[] = $conditioinIdIdIndex;
                        }
                        $servicePriceList = $model->getElements(
                            "SELECT c.condition_service_price, s.service_name FROM condition_service c INNER JOIN service s ON s.service_id = c.service_id WHERE c.condition_id_id = ?",
                            $conditioinIdId
                        );
                        // ($name, $customer_id, $email, $coins, $readingDeviceList, $servicePriceList, $vehicleType, $vehicleBrand, $vehicleModel, $ecu, $messageChosenNothing = false)
                        //  ($mark, $_SESSION['customer_id'], $email, $coins, $readingDeviceList, $servicePriceList, $_POST['vehicle_type'], $_POST['vehicle_brand'], $_POST['vehicle_model'], $_POST['ecu'])
                        $cookiesmanagement->customerSessionStart();
                        $email = $model->getElements(
                            "SELECT customer_email FROM customer WHERE customer_id = ?",
                            [$_SESSION['customer_id']]
                        )[0]['customer_email'];
                        $coins = $model->getElements(
                            "SELECT customer_coins FROM customer WHERE customer_id = ?",
                            [$_SESSION['customer_id']]
                        )[0]['customer_coins'];
                        return (new PageCustomerCabinetAllparameters($mark, $_SESSION['customer_id'], $email, $coins, $readingDeviceList, $servicePriceList, $_POST['vehicle_type'], $_POST['vehicle_brand'], $_POST['vehicle_model'], $_POST['ecu']))->getHTML();
                    }
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
                    $cookiesmanagement->customerSessionStart();
                    $email = $model->getElements(
                        "SELECT customer_email FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_email'];
                    $coins = $model->getElements(
                        "SELECT customer_coins FROM customer WHERE customer_id = ?",
                        [$_SESSION['customer_id']]
                    )[0]['customer_coins'];
                    return (new PageCustomerCabinetEcu($mark, $_SESSION['customer_id'], $email, $coins, $dataNameArr, $_POST['vehicle_type'], $_POST['vehicle_brand'], $_POST['vehicle_model']))->getHTML();
                }
                if (self::checkMethodPostAndPageName('Allparameters')) {
                    $service_type_name = 'file treatment';

                    $order_status = 'unpaid';
                    $date = time();
                    $service_type_id = $model->getElements(
                        "SELECT service_type_id FROM service_type WHERE service_type_name = ?",
                        [$service_type_name]
                    );
                    if (!array_key_exists('total_sum', $_POST)) {
                        return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
                    }
                    if ($_POST['total_sum'] < 1) {
                        // здесь должна подтянуться страница выдачи allParameters
                    }
                    $customer_order_amount = $_POST['total_sum'];
                     
                    $cookiesmanagement->customerSessionStart();
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
                            $customer_order_amount, 
                            $date, 
                            $order_status
                        ]
                    );
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
                                $message_content,
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
                            $customer_order_data_value = $_POST[$customer_order_data_name];
                            $model->addElements(
                                "INSERT INTO customer_order_data (
                                    customer_order_id, 
                                    customer_order_data_name, 
                                    customer_order_data_value
                                    ) VALUES 
                                    (?, ?, ?)",
                                [
                                    $customer_order_id, 
                                    $customer_order_data_name, 
                                    $customer_order_data_value 
                                ]
                            );
                        }
                    }
                    $stringOfServices = $_POST['ServiceSet'];
                    $arrOfServices = explode(', ', $stringOfServices);
                    foreach ($arrOfServices as $service) {
                        if (array_key_exists($service, $_POST) && $_POST[$service] === 'on') {
                            $model->addElements(
                                "INSERT INTO customer_order_service (customer_order_id, service_name) VALUES (?, ?)",
                                [$customer_order_id, $service]
                            );
                        }
                    }
                    
                }
            }
            if ($mark == 'Dealsdeals') {
                if (self::checkMethodPostAndPageName('Dealsdeals')) {
                    return self::getDealCard($receiveBD, $model, $mark);
                }
                $fromPageArr = ['Pay', 'Dealsdeal', 'Treatment', 'Profile', 'Profilenotupdated', 'Profileupdated', 'History', 'Bigfile'];
                return self::routingSimpleDealsdeals($fromPageArr, $cookiesmanagement, $model, $receiveBD);
            }
            if ($mark == 'Dealsdeal') {
                if (self::checkMethodPostAndPageName('Dealsdeal')) {
                    $cookiesmanagement->customerSessionStart();
                    if (array_key_exists('payService', $_POST)) {
                        $status = $model->getElementsByOneParameterWithNameArr($receiveBD, 'order_status', 'orders', 'order_id', $_SESSION['order_id'], 'order_id')[0]['order_status'];
                        if ('unpaid' == $status) {
                            $model->updateCoins((-$_POST['payService']), $_SESSION['customer_id']);
                            $model->updateElementByUniqueParameter($receiveBD,  'orders', 'order_status', 'paid', 'order_id', $_SESSION['order_id']);
                            $model->addElement($receiveBD, 'pay_order', [
                                'order_id' => $_SESSION['order_id'],
                                'pay_order_date' => time(),
                                'pay_order_sum' => $_POST['payService']
                            ]);
                        }
                    }
                    if (array_key_exists('comment', $_POST)) {
                        $model->addElement($receiveBD, 'messages', [
                            'order_item_id' => $_POST['id_item'],
                            'message_content' => $_POST['comment'], 
                            'message_from' => 'customer',
                            'message_date' => time(),
                            'message_seen' => 0,
                        ]);
                    }
                    return self::getDealCard($receiveBD, $model, $mark);
                }
                return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
            }
            if ($mark == 'History') {
                $fromPageArr = ['Pay', 'Dealsdeal', 'Treatment', 'Profile', 'Profilenotupdated', 'Profileupdated', 'History', 'Bigfile', 'Dealsdeals'];
                return self::routingSimpleHistory($fromPageArr, $cookiesmanagement, $model, $receiveBD);

            }
            if ($mark == 'Profile') {
                if (self::checkMethodPostAndPageName('Profile')) {
                    PageCustomerCabinetProfile::$externalConditionLoginExist = $model->checkLogin($receiveBD, $_POST['Login']);
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
                    $cookiesmanagement->customerSessionStart();
                    $coins = $model->getCustomerElementById($receiveBD, 'customer_coins', $_SESSION['customer_id']);
                    $login = $model->getCustomerElementById($receiveBD, 'customer_login', $_SESSION['customer_id']);
                    if ( self::checkAnalogOr($arrProfileMessage) )   {
                        $tel = $model->getCustomerElementById($receiveBD, 'customer_telephone', $_SESSION['customer_id']);
                        $email = $model->getCustomerElementById($receiveBD, 'customer_email', $_SESSION['customer_id']);
                        $valuta = $model->getCustomerElementById($receiveBD, 'customer_valuta', $_SESSION['customer_id']);
                        $arrProfile = [
                            'Login' => $login,
                            'Tel' => $tel,
                            'Email' => $email,
                            'Valuta' => $valuta
                        ];
                        return (new PageCustomerCabinetProfile($mark, $_SESSION['customer_id'], $login, $coins, $arrProfile, $arrProfileMessage))->getHTML();
                    }
                    if (count($arrProfileMessage) == 0) {
                        return (new PageCustomerCabinetProfilenotupdated($mark, $_SESSION['customer_id'], $login, $coins, $arrProfileMessage))->getHTML();
                    }
                    $updatingParameters = [
                        'Login' => 'customer_login', 
                        'Lang' => 'customer_language', 
                        'Tel' => 'customer_telephone',
                        'Email' => 'customer_email', 
                        'Valuta' => 'customer_valuta', 
                        'Pass' => 'customer_password'
                    ];
                    foreach ($updatingParameters as $parameterName => $bdParameterName) {
                        if (array_key_exists($parameterName, $_POST) && $_POST[$parameterName] != '') {
                            $model->updateCustomerElementById($receiveBD, $bdParameterName, $_POST[$parameterName], $_SESSION['customer_id']);
                        }
                    }
                    $login = $model->getCustomerElementById($receiveBD, 'customer_login', $_SESSION['customer_id']);
                    return (new PageCustomerCabinetProfileupdated($mark, $_SESSION['customer_id'], $login, $coins))->getHTML();
                }
                $fromPageArr = ['Pay', 'Dealsdeal', 'Treatment', 'Profilenotupdated', 'Profileupdated', 'History', 'Bigfile', 'Dealsdeals'];
                return self::routingSimpleProfile($fromPageArr, $cookiesmanagement, $model, $receiveBD);
            }
            if ($mark == 'Uploadprovfile') {
                if (self::checkMethodPostAndPageName('Dealsdeal')) {
                    if (array_key_exists('id_item', $_POST)) {
                        return self::uploadFile($mark, $receiveBD, $model, $_POST['id_item'], 'treated_file');
                        
                    } 
                }
            }

// админская часть 

            if ($mark == 'Gate') {
                if ($_SERVER['REQUEST_METHOD'] == "GET") {
                    return (new PageProviderGate($mark))->getHTML();
                }
                return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
            }
            if ($mark == 'Admin') {
                $pages = ['Admin', 'Deal', 'Deals', 'Gate', 'Valuta'];
                foreach ($pages as $page) {
                    if (self::checkMethodPostAndPageName($page)) {
                        if ($page == 'Gate') {
                            if (self::checkPostMethodValue('pass', self::$pppp)) {
                                session_start();
                                $_SESSION['pass'] = self::$pppp;
                                return self::getAdminPage($receiveBD, $mark, $model);
                            }
                            return (new PageProviderGate($mark, true))->getHTML();
                        }
                        return self::getAdminPageWithSession($receiveBD, $mark, $model);
                    }
                }
            }
            if ($mark == 'Deal') {
                $pages = ['Admin', 'Deal', 'Deals', 'Valuta'];
                foreach ($pages as $page) {
                    if (self::checkMethodPostAndPageName($page)) {
                        if ($page == 'Deal') {
                            if (self::checkExistPosts(['comment', 'order_id', 'id_item'])) {
                                return self::getDealPageWithSession($receiveBD, $mark, $model, true, null);
                            }
                            if (self::checkExistPosts(['message_id', 'order_id', 'id_item', 'button_message_seen'])) {
                                if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                                    return $gettingPage;
                                }
                                if ($model->updateElementByUniqueParameter($receiveBD, 'messages' , 'message_seen', 1, 'message_id', $_POST['message_id'])) {
                                    return self::getDealPage($receiveBD, $mark, $model, null);
                                }
                            }
                            if (self::checkExistPosts(['order_id', 'status_switch'])) {
                                if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                                    return $gettingPage;
                                }
                                if ($model->getElementByUniqueParameter($receiveBD, 'order_status' , 'orders', 'order_id', $_POST['order_id']) == $_POST['status_switch']) {
                                    return self::getDealPage($receiveBD, $mark, $model, null);
                                }
                                if ($model->updateElementByUniqueParameter($receiveBD, 'orders' , 'order_status', $_POST['status_switch'], 'order_id', $_POST['order_id'])) {
                                    return self::getDealPage($receiveBD, $mark, $model, null);
                                }
                            }
                            if (self::checkExistPosts(['order_id', 'link-download'])){
                                return self::getDealPageWithSession($receiveBD, $mark, $model, false, $_POST['link-download']);
                            }
                        } else {
                            if (self::checkExistPosts(['order_id'])) {
                                return self::getDealPageWithSession($receiveBD, $mark, $model, false, null);
                            }
                        }
                    }
                }
            }
            if ($mark == 'Uploadcustfile') {
                if (self::checkMethodPostAndPageName('Deal')) {
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    if (self::checkExistPosts(['order_id', 'link-download'])){
                        return self::uploadFile($mark, $receiveBD, $model, $_POST['link-download'], 'file');
                    }
                }
            }
            if ($mark == 'Downloadprovfile') {
                if (self::checkMethodPostAndPageName('Deal')) {
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    if (self::checkExistPosts(['order_id', 'link-load'])){
                        $customerId = $model->getElementByUniqueParameter($receiveBD, 'customer_id', 'orders', 'order_id', $_POST['order_id']);
                        $date = time();
                        $day = date('Y-m-d', $date);
                        $dirPathWithoutRootPath = $GLOBALS['outgoingFileDir'] . '/' . $customerId . '/' . $_POST['order_id'] . '/' . $_POST['link-load'] . '/' . $day;
                        $dirPath = $GLOBALS['saveFilePath'] . $dirPathWithoutRootPath;
                        if (!is_dir($dirPath)) {
                            if (!mkdir($dirPath, 0777, true)) {
                                return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
                            }
                        }
                        $file_extension = strrchr($_FILES['treatedfile']['name'], '.');
                        $path_to_file = $dirPath . '/' . $_POST['link-load'] . '_' . $date . '_treated' . $file_extension;
                        $path_to_file_WithoutRootPath = $dirPathWithoutRootPath . '/' . $_POST['link-load'] . '_' . $date . '_treated'. $file_extension;
                        if ($_FILES['treatedfile']['size'] < $GLOBALS['fileSizeFromCustomer']) {
                            if (move_uploaded_file($_FILES['treatedfile']['tmp_name'],  $path_to_file)) {
                                $open_resurs = fopen($path_to_file, 'rb');
                                $file_is_string = fread($open_resurs, $GLOBALS['fileSizeFromCustomer']);
                                if (customCRC16($file_is_string) == $_POST['checksumtreatedfile']) {
                                    $model->updateElementByUniqueParameter($receiveBD,  'file_processing', 'file_processing_path_to_treated_file', $path_to_file_WithoutRootPath, 'order_item_id', $_POST['link-load']);
                                    return self::getDealPage($receiveBD, $mark, $model, null);

                                } else {
                                    unlink($path_to_file);
                                    return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
                                }
                            } else {
                                $errorCode = $_FILES['treatedfile']['error'];
                                if ($errorCode === 1 || $errorCode === 2) {
                                    return 'Файл слишком большой';
                                }
                                return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
                            }
                        } else {
                            return 'Файл слишком большой';
                        }
                    }
                }
            }

            if ($mark == 'Deals') {
                if (self::checkMethodPostAndPageName('Deal')) {
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    $parameters = $model->getAllDeals($receiveBD);
                    return (new PageProviderDeals($mark, $parameters))->getHTML();
                }
                if (self::checkMethodPostAndPageName('Admin')) {
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    $parameters = $model->getAllDeals($receiveBD);
                    return (new PageProviderDeals($mark, $parameters))->getHTML();
                }
                if (self::checkMethodPostAndPageName('Valuta')) {
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    $parameters = $model->getAllDeals($receiveBD);
                    return (new PageProviderDeals($mark, $parameters))->getHTML();
                }
                if (self::checkMethodPostAndPageName('Deals')) {
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    $parameters = $model->getAllDeals($receiveBD);
                    return (new PageProviderDeals($mark, $parameters))->getHTML();
                }
            }

            if ($mark == 'Valuta') {
                $pages = ['Admin', 'Deal', 'Deals', 'Valuta'];
                foreach ($pages as $page) {
                    if (self::checkMethodPostAndPageName($page)) {
                        if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                            return $gettingPage;
                        }
                        if ($page == 'Valuta' && self::checkExistPosts(['Valuta', $GLOBALS["rub"], $GLOBALS["usd"], $GLOBALS["eur"]])) {
                            $valutes = [$GLOBALS["rub"], $GLOBALS["usd"], $GLOBALS["eur"]];
                            foreach($valutes as $valuta) {
                                if ($model->getOneValutaValue($receiveBD, $valuta)) {
                                    $model->updateValuta($receiveBD, $_POST[$valuta], $valuta);
                                } else {
                                    $model->addValuta($receiveBD, $_POST[$valuta], $valuta);
                                }
                            }
                        }
                        return self::getValutaPage($receiveBD, $mark, $model);
                    }
                }
            }

            return self::routingSimplePage($arrAllCustomerPage, $mark);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
        
    }
    static function printPage()
    {
        echo self::getPage();
    }
}