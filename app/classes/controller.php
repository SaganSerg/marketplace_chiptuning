<?php
abstract class Controller
{
    // static private $pppp = '123456';

    static private $allCustomerCabinetPage = [
        '/allparameters', 
        '/brand', 
        '/dealsdeal', 
        '/dealsdeals', 
        '/ecu', 
        '/history', 
        '/model', 
        '/pay', 
        '/profile', 
        '/treatment',
        '/payisbad',
        '/payisgood'
    ];

    static private $allCustomerFacadePage = [
        '/contacts', 
        '/index', 
        '/newpassword', 
        '/notfound', 
        '/rememberpassword', 
        '/sentmail', 
        '/sentmailregistration', 
        '/messagesentmailregistration', 
        '/termsuse', 
        '/about', 
        '/registration',
        '/payisbadf',
        '/wronglink'
    ];

    static private $allProviderPage = [
        '/admin', 
        '/deal', 
        '/deals', 
        '/gate', 
        '/valuta'
    ];

    // static function getDataFromOurerService(string $url, array $requestBody)
    // {
    //     $init = curl_init($url);
    //     if ($init) {
    //         if 
    //         (
    //             curl_setopt($init, CURLOPT_RETURNTRANSFER, true) &&
    //             curl_setopt($init, CURLOPT_POST, true) &&
    //             curl_setopt($init, CURLOPT_POSTFIELDS, $requestBody)
    //         ) 
    //         {
    //             if ($json = curl_exec($init)) {
    //                 $text = json_decode($json, true);
    //                 if (json_last_error() === 0) {
    //                     return $text;
    //                 }
    //             }
    //         }
    //     }
    // }
    static function getJSONfromOuterService(string $url, array $requestBody)
    {
        $init = curl_init($url);
        if ($init) {
            if 
            (
                curl_setopt($init, CURLOPT_RETURNTRANSFER, true) &&
                curl_setopt($init, CURLOPT_POST, true) &&
                curl_setopt($init, CURLOPT_POSTFIELDS, $requestBody)
            ) 
            {
                if ($json = curl_exec($init)) {
                    // echo $json, '<br>';
                    return $json; 
                }
            }
        }
    }

    static function uploadFile($mark, $model, $customer_order_id)
    {
        $path = $model->getElements(
            "SELECT * FROM file_path WHERE customer_order_id = ? AND file_path_what_file = ?",
            [$customer_order_id, $GLOBALS['notTreatmentedFile']]
        );
        $checkCountPath = count($path); 
        if ($checkCountPath == 0) {
            return "В таблице c местоположением файла для данной сделки не хранится ни одна запись"; // надо создать страничку
        }
        if (count($path) > 1) {
            return 'В таблице c местоположением файла хранится больше чем одна запись с для одной сделки'; // надо создать страничку
        }

        $fullPath = $GLOBALS['saveFilePath'] . $path[0]['file_path_path'];

        if (ob_get_level()) {
            ob_end_clean();
        }
        $fileSize = filesize($fullPath);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($fullPath));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $fileSize);
        
        $handle = fopen($fullPath, "rb");
        $contents = fread($handle, $fileSize);
        fclose($handle);
        return $contents;
    }

    static function uploadTreatedFile ($mark, $model, $customer_order_id)
    {
        $path = $model->getElements(
            "SELECT * FROM file_path WHERE customer_order_id = ? AND file_path_what_file = ?",
            [$customer_order_id, $GLOBALS['treatmentedFile']] // treatmentedFile это должно быть заменено
        );
        $checkCountPath = count($path); 
        if ($checkCountPath == 0) {
            return self::getNotFound($mark); // это должно быть заменено
        }
        if (count($path) > 1) {
            return self::getNotFound($mark); // это должно быть заменено
        }
        $fullPath = $GLOBALS['saveFilePath'] . $path[0]['file_path_path'];

        if (ob_get_level()) {
            ob_end_clean();
        }
        $fileSize = filesize($fullPath);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($fullPath));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $fileSize);
        
        $handle = fopen($fullPath, "rb");
        $contents = fread($handle, $fileSize);
        fclose($handle);
        return $contents;
    }
    static function startSessionAndCheckPassForProv($mark)
    {
        session_start();
        if (!array_key_exists('provider_id', $_SESSION)) {
            return (new PageProviderGate($mark, true))->getHTML();
        }
    }
    static function getDealPageWithSession ($mark, $model, $ifAddComment)
    {
        if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
            return $gettingPage;
        }
        $provider_id = $model->getElements(
            "SELECT provider_id FROM customer_order WHERE customer_order_id = ?",
            [$_POST['customer_order_id']]
        )[0]['provider_id'];
        if ($provider_id == $_SESSION['provider_id'] || $provider_id == null) { // надо будет убедиться что данный код работает, а то я не знаю что реально возвращается, когда в ячейке ничего не хранится
            $date = time();
            if ($ifAddComment) {
                $model->addElements(
                    "INSERT INTO message (
                        customer_order_id, 
                        message_content, 
                        message_from, 
                        message_date,
                        message_seen
                    ) VALUES (
                        ?, ?, ?, ?, ?
                    )",
                    [$_POST['customer_order_id'], $_POST['message_content'], 'provider', $date, 0]
                );
            }
        } else {
            return (new PageProviderNoright($mark))->getHTML();
        }
        return self::getDealPage($mark, $model, '', $_SESSION['provider_id']);
    }
    static function getDealPage($mark, $model, $uploadFileMessage = '', $sessionProviderId = null)
    {
        $customer_id = $model->getElements(
            "SELECT * FROM customer_order WHERE customer_order_id = ?",
            [$_POST['customer_order_id']]
        )[0]['customer_id'];

        $customerParameters = $model->getElements(
            "SELECT * FROM customer WHERE customer_id = ?",
            [$customer_id]
        )[0];
        $dealSpecification = $model->getElements(
            "SELECT * FROM customer_order WHERE customer_order_id = ?",
            [$_POST['customer_order_id']]
        )[0];
        $parameterList = $model->getElements(
            "SELECT * FROM customer_order_data WHERE customer_order_id = ?",
            [$_POST['customer_order_id']]
        );
        $serviceList = $model->getElements(
            "SELECT * FROM customer_order_service WHERE customer_order_id = ?",
            [$_POST['customer_order_id']]
        );
        $messageList = $model->getElements(
            "SELECT * FROM message WHERE customer_order_id = ?",
            [$_POST['customer_order_id']]
        );
        $fileData = $model->getElements(
            "SELECT * FROM file_path WHERE customer_order_id = ?",
            [$_POST['customer_order_id']]
        )[0];
        $fileData = $fileData ? $fileData : [];
        return (new PageProviderDeal($mark, $customerParameters, $dealSpecification, $parameterList, $serviceList, $messageList, $uploadFileMessage, $sessionProviderId, $fileData))->getHTML();
    }
    static function getAdminPage($mark, $model, $provider_id)
    {
        $unpaidDealsProviderWithout = $model->getElements(
            "SELECT * FROM customer_order WHERE customer_order_status = ? AND provider_id IS NULL ORDER BY customer_order_date",
            [$GLOBALS['unpaidDealStatus']]
        );
        $paidDealsProviderWithout = $model->getElements(
            'SELECT * FROM customer_order WHERE customer_order_status = ? AND provider_id IS NULL ORDER BY customer_order_date',
            [$GLOBALS['paidDealStatus']]
        );
        // $withoutProviderOrders = $model->getElements(
        //     "SELECT customer_order_id FROM customer_order WHERE provider_id IS NULL",
        //     []
        // );
        // $notMessageSeenDealsProviderWithout = [];
        // foreach ($withoutProviderOrders as $orders) {
        //     $notMessageSeenDealsProviderWithout[] = $model->getElements(
        //         "SELECT * FROM message WHERE message_seen = 0 AND customer_order_id = ? ORDER BY message_date",
        //         [$orders['customer_order_id']]
        //     );
        // }
        $notSeenMessageCustomerOrderIds = $model->getElements(
            "SELECT DISTINCT customer_order_id FROM message WHERE message_seen = 0",
            []
        );
        $customerOrderProviderWithout = $model->getElements(
            "SELECT * FROM customer_order WHERE provider_id IS NULL ORDER BY customer_order_date",
            []
        );
        $notMessageSeenDealsProviderWithout = [];
        foreach ($notSeenMessageCustomerOrderIds as $id) {
            foreach ($customerOrderProviderWithout as $order) {
                if ($id['customer_order_id'] == $order['customer_order_id']) {
                    $notMessageSeenDealsProviderWithout[] = $order;
                }
            }
        }
        // $notMessageSeenDealsProviderWithout = [];
        // foreach ($notSeenMessageCustomerOrderIds as $order) {
        //     $notMessageSeenDealsProviderWithout[] = $model->getElements(
        //         "SELECT * FROM customer_order WHERE customer_order_id = ? ORDER BY customer_order_date",
        //         [$order['customer_order_id']]
        //     )[0];
        // }
        $unpaidDealsProviderWith = $model->getElements(
            "SELECT * FROM customer_order WHERE customer_order_status = ? AND provider_id = ? ORDER BY customer_order_date",
            [$GLOBALS['unpaidDealStatus'], $provider_id]
        );
        $paidDealsProviderWith = $model->getElements(
            'SELECT * FROM customer_order WHERE customer_order_status = ? AND provider_id = ? ORDER BY customer_order_date',
            [$GLOBALS['paidDealStatus'], $provider_id]
        );


        // $notSeenMessages = $model->getElements(
        //     "SELECT * FROM message WHERE message_seen = 0",
        //     []
        // );
        // $customerOrderIds = $model->getElements(
        //     "SELECT customer_order_id FROM customer_order WHERE provider_id = ?",
        //     [$provider_id]
        // );

        // $notMessageSeenDealsProviderWith = [];
        // foreach ($notSeenMessages as $message) {
        //     foreach ($customerOrderIds as $customerOrderId)
        //     if ($message['customer_order_id'] == $customerOrderId['customer_order_id']) {
        //         $notMessageSeenDealsProviderWith[] = $message;
        //     }
        // }
        // $notMessageSeenDealsProviderWith = array_unique($notMessageSeenDealsProviderWith, SORT_REGULAR);

        $notSeenMessageCustomerOrderIdsProviderWith = $model->getElements(
            "SELECT DISTINCT customer_order_id FROM message WHERE message_seen = 0",
            []
        );
        $customerOrderProviderWith = $model->getElements(
            "SELECT * FROM customer_order WHERE provider_id = ? ORDER BY customer_order_date",
            [$provider_id]
        );
        $notMessageSeenDealsProviderWith = [];
        foreach ($notSeenMessageCustomerOrderIdsProviderWith as $id) {
            foreach ($customerOrderProviderWith as $order) {
                if ($id['customer_order_id'] == $order['customer_order_id']) {
                    $notMessageSeenDealsProviderWith[] = $order;
                }
            }
        }

        $beingDoneDealsProviderWith = $model->getElements(
            "SELECT * FROM customer_order WHERE customer_order_status = ? AND provider_id = ? ORDER BY customer_order_date",
            [$GLOBALS['beingDoneDealStatus'], $provider_id]
        );
        /* 
        $unpaidDealsProviderWithout, 
        $paidDealsProviderWithout, 
        $notMessageSeenDealsProviderWithout, 
        $unpaidDealsProviderWith, 
        $paidDealsProviderWith, 
        $notMessageSeenDealsProviderWith, 
        $beingDoneDealsProviderWith
        */
        return (new PageProviderAdmin(
            $mark, 
            $unpaidDealsProviderWithout, 
            $paidDealsProviderWithout, 
            $notMessageSeenDealsProviderWithout, 
            $unpaidDealsProviderWith, 
            $paidDealsProviderWith, 
            $notMessageSeenDealsProviderWith, 
            $beingDoneDealsProviderWith
            ))->getHTML();
    }
    static function getAdminPageWithSession($mark, $model)
    {
        session_start();
        if (!array_key_exists('provider_id', $_SESSION)) {
            return (new PageProviderGate($mark, true))->getHTML();
        }
        return self::getAdminPage($mark, $model, $_SESSION['provider_id']);
    } // это не закончено

    static private function checkExistPosts($arr)
    {
        foreach ($arr as $elem) {
            if (!array_key_exists($elem, $_POST)) {
                return false;
            }
        }
        return true;
    }


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
                        "SELECT coin_transaction_id, coin_transaction_date, coin_transaction_sum, coin_transaction_status FROM coin_transaction WHERE customer_id = ? AND (coin_transaction_status = 'PaySystemOK' OR coin_transaction_status = 'downCoins')",
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


    // данная функция применяется для маршрутизации по сайту когда при переходе на страницу не нужно передавать никаки дополнительных параметров т.е. когда контент целевой страницы не меняется, например, содержание главной страницы не меняется с какой страницы Вы бы на нее не перешли
    static function routingSimplePage(array $withExceptionOf, string $toPage, bool $checkReferer, array ...$pageArr) // этот метод для страниц 'Notfound', 'About', 'Contacts', 'Termsuse'
    // первый аргумент, это массив с перечнем $pageName с которых не должно быть переходов на страницу $toPage
    // второй аргумент должен быть в виде /about т.е. в виде строки со слешем в начале как это сделано при свойствах $pageName классов унаследованных от PageCustomer
    // это проверка заголовка referrer это защита от атак
    // после третьего параметра идут массивы в которых указаны страницы с которых возможен переход на целевую страницу
    {
        if (self::checkMethodName('Page', 'POST', $_POST) && $checkReferer) {
            $allPage = [];
            foreach ($pageArr as $elem) {
                $allPage = array_merge($allPage, $elem);
            }
            $fromPageArr = [];
            foreach ($allPage as $pageNumber => $pageValue) {
                foreach ($withExceptionOf as $page) {
                    if ($page == $pageValue) {
                        $pageValue = null;
                    }
                }
                if ($pageValue !== null) {
                    $fromPageArr[] = $pageValue;
                }
            }
            foreach ($fromPageArr as $page) {
                if ($_POST['Page'] == $page) {
                    $preparedPageName = ucfirst(substr($toPage, 1)); // это вырезается первый слеш и делает первую букву большой, чтобы можно было сформировать имя класса
                    $class = 'PageCustomerFacade' . $preparedPageName;
                    return (new $class($toPage, null))->getHTML();
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
        $model->updateElements(
            "UPDATE message SET message_seen = 1 WHERE message_from = 'provider' AND message_seen = 0",
            []
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
        $day = date('Y-m-d-H-i-s', $date);
        $dirPathWithoutRootPath = $GLOBALS['incomingFileDir'] . '/' . $_SESSION['customer_id'] . '/' . $customer_order_id . '/' . $day;
        $dirPath = $GLOBALS['saveFilePath'] . $dirPathWithoutRootPath;
        $open_resurs = fopen($_FILES['original_file']['tmp_name'], 'rb');
        $file_is_string = fread($open_resurs, $GLOBALS['fileSizeFromCustomer']);
        if (customCRC16($file_is_string) == $_POST['checksum']) {
            if (!is_dir($dirPath)) {
                if (!mkdir($dirPath, $GLOBALS['accessRight'], true)) {
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
                        file_path_what_file,
                        file_path_checksum
                        ) VALUES (?, ?, ?, ?, ?)",
                    [
                        $model->cleanForm($customer_order_id),
                        $model->cleanForm($date),
                        $path_to_file_WithoutRootPath,
                        $GLOBALS['notTreatmentecFile'],
                        $_POST['checksum']
                    ]
                );
                return self::getDealCard3($model, $mark);
            }
        }
        return self::getNotFound($mark);
    }

    static function addFile4 (int $date, int $customer_order_id, string $mark, Model $model, string $email, string $coins)
    {
        
            
                
                
                    $customerId = $model->getElements(
                        'SELECT * FROM customer_order WHERE customer_order_id = ?',
                        [$customer_order_id] // 'customer_order_id
                    )[0]['customer_id']; 
                    $dirPathWithoutRootPath = $GLOBALS['incomingFileDir'] . '/' . $customerId . '/' . $customer_order_id; // 'incomingFileDir' это различие
                    $dirPath = $GLOBALS['saveFilePath'] . $dirPathWithoutRootPath; // 'saveFilePath' это различие
                    if (!is_dir($dirPath)) {
                        if (!mkdir($dirPath, $GLOBALS['accessRight'], true)) {
                            return self::getNotFound($mark); // не получилось создать директорию это различие
                        }
                    }
                    $file_extension = strrchr($_FILES['original_file']['name'], '.'); // original_file различие 
                    $fileName = $customerId . '_' . $customer_order_id  . '_' . $date . $file_extension; // строка с точкой '.' это различие
                    $fileNameWithSlash = "/" . $fileName;
                    $path_to_file = $dirPath . $fileNameWithSlash;
                    $path_to_file_WithoutRootPath = $dirPathWithoutRootPath . $fileNameWithSlash;
                    if ($_FILES['original_file']['size'] < $GLOBALS['fileSizeFromCustomer']) { // различие original_file
                        if (move_uploaded_file($_FILES['original_file']['tmp_name'],  $path_to_file)) { // различие original_file
                            $open_resurs = fopen($path_to_file, 'rb');
                            $file_is_string = fread($open_resurs, $GLOBALS['fileSizeFromCustomer']);
                            if (customCRC16($file_is_string) == $_POST['checksum']) { // 'checksum' это различие
                                $isFilePath = $model->getElements(
                                    "SELECT * FROM file_path WHERE customer_order_id = ? AND file_path_what_file = ?",
                                    [$customer_order_id, $GLOBALS['notTreatmentedFile']] // 'notTreatmentedFile' $_POST customer_order_id это различие
                                );
                                if (count($isFilePath) > 1) {
                                    return self::getNotFound($mark); // "В базе зафиксировано несколько путей сохранения файлов для данной сделки" различие
                                }
                                if (!$isFilePath) {
                                    $updatedCount = $model->addElements(
                                        "INSERT INTO file_path (customer_order_id, file_path_date, file_path_path, file_path_what_file, file_path_checksum) VALUES (?, ?, ?, ?, ?)",
                                        [$customer_order_id, $date, $path_to_file_WithoutRootPath, $GLOBALS['notTreatmentedFile'], $_POST['checksum']] // 'notTreatmentedFile' и 'checksum' различие
                                    );
                                } else {
                                    $oldFilePath = $GLOBALS['saveFilePath'] . $isFilePath[0]['file_path_path'];
                                    if (!unlink($oldFilePath)) {
                                        return self::getNotFound($mark); // 'не получилось удалить старый файл, но новый файл создан, но путь в базе остался старый' различие
                                    }
                                    $updatedCount = $model->updateElements(
                                        "UPDATE file_path SET file_path_date = ?, file_path_path = ? WHERE customer_order_id = ? AND file_path_what_file = ?",
                                        [$date, $path_to_file_WithoutRootPath, 'customer_order_id', $GLOBALS['notTreatmentedFile']] // 'customer_order_id' и 'notTreatmentedFile' -- различие
                                    );
                                }
                                if (!$updatedCount) {
                                    return self::getNotFound($mark); // 'Не получилось записать в базу данный путь до файла' 
                                }
                                // этот участок пока не доделан
                                if ($_FILES['original_file']['error'] === 0) { // различие original_file
                                    return self::getDealCard3($model, $mark); // в этом месте может быть другой метод различие
                                }
                                return self::getNotFound($mark); // в этом месте может быть другой метод и сообщение $_FILES['treatedfile']['error'] не равен 0
                            } else {
                                unlink($path_to_file);
                                rmdir($dirPath);
                                return self::getNotFound($mark); // 'Чек суммы не совпали. файл и директории удалены. Надо попробовать еще раз'; другой метод
                            }
                        } else {
                            $errorCode = $_FILES['original_file']['error']; // различие original_file
                            if ($errorCode === 1 || $errorCode === 2) {
                                return self::getNotFound($mark); // 'Файл слишком большой' различие
                            }
                            if ($errorCode === 4) {
                                return self::getNotFound($mark); // 'Файл не был загружен' различие
                            }
                            if ($errorCode === 3) {
                                return self::getNotFound($mark); // 'Файл загружен частично' различие
                            }
                            if ($errorCode === 7) {
                                return self::getNotFound($mark); // Не удалось записать файл на диск' различие
                            }
                            if ($errorCode === 6) {
                                return self::getNotFound($mark); // Отсутствует временная папка ' различие
                            }
                            if ($errorCode === 8) {
                                return self::getNotFound($mark); // При загрузке файла что-то пошло не так, но что именно не ясно
                            }
                            return self::getNotFound($mark);
                        }
                    } else {
                        return self::getNotFound($mark); // файл слишком
                    }
                
            
        
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
                        [1, $dateLinkCreation['customer_password_recovery_id']]
                    );
                }
            }
            $alterableURL = (
                ($customerPasswordRecoveryUrl = ($customerPasswordRecoveryId = $model->getCustomerPasswordRecoveryId($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : false)
                ||
                ($email_for_registration_url = ($email_for_registration_id = $model->getRegistrationEmailId($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : false)
                ||
                ($paySystemIsBadUrl = ($paySysterIsBadId = $model->getCoinTransactionId($_SERVER['REQUEST_URI'], 'payisbad')) ? $_SERVER['REQUEST_URI'] : false)
                ||
                ($paySystemIsGoodUrl = ($paySystemIsGoodId = $model->getCoinTransactionId($_SERVER['REQUEST_URI'], 'payisgood')) ? $_SERVER['REQUEST_URI'] : false)
            );
            $cookiesmanagement = $GLOBALS['cookiesmanagement'];
            $mark = '/notfound';
            switch ($_SERVER['REQUEST_URI']) {
                
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
                // case '/sentmail':
                //     $mark = '/sentmail';
                // break;
                case '/sentmailregistration':
                    $mark = '/sentmailregistration';
                break;
            }
/* 
При описании маршрутизации страниц из внешней части нужно обязательно делать ссылку на саму себя, для переключения языка
*/
            if ($_SERVER['REQUEST_URI'] == '/index'     || 
                $_SERVER['REQUEST_URI'] == ''           || 
                $_SERVER['REQUEST_URI'] == '/'          || 
                $_SERVER['REQUEST_URI'] == '/index.php' || 
                $_SERVER['REQUEST_URI'] == '/index.html') {
                $mark = '/index';
                if ($_SERVER['REQUEST_METHOD'] == "GET") {
                    return (new PageCustomerFacadeIndex($mark, null))->getHTML();
                }
                return self::routingSimplePage([], $mark, $checkReferer, self::$allCustomerFacadePage, self::$allCustomerCabinetPage);
            }
            // if ($_SERVER['REQUEST_URI'] == '/payisgood') { // нужно данную строку сохранить в переменной
            //     $mark = '/payisgood';

            //     if ($_SERVER['REQUEST_METHOD'] == "GET") {
            //         session_start();
            //         if (isset($_SESSION['customer_id'])) {
            //             $customerData = $model->getElements(
            //                 'SELECT * FROM customer WHERE customer_id = ?',
            //                 [$_SESSION['customer_id']]
            //             )[0];
            //             return (new PageCustomerCabinetPayisgood($mark, $_SESSION['customer_id'], $customerData['customer_email'], $customerData['customer_coins']))->getHTML();
            //         }
            //         return (new PageCustomerFacadeIndex($mark, null))->getHTML();
            //     }
            //     return self::getNotFound($mark);
            // }

            if ($_SERVER['REQUEST_URI'] == '/about') {
                $mark = '/about';

                return self::routingSimplePage([], $mark, $checkReferer, self::$allCustomerFacadePage);
            }

            if ($_SERVER['REQUEST_URI'] == '/contacts') {
                $mark = '/contacts';

                return self::routingSimplePage([], $mark, $checkReferer, self::$allCustomerFacadePage);
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
                    $message = '
                        <html>
                        <head>
                          <title>' . self::getText($lang, 'Registration on the website', $arrPhrases) . '</title>
                        </head>
                        <body>
                          <p>' . self::getText($lang, 'Registration on the website', $arrPhrases) . '</p>
                          <table>
                            <tr>
                              <td> ' . self::getText($lang, 'In order to register in our system, you need to follow the link', $arrPhrases) . '<a href="' . $GLOBALS['protocol'] . '://' . $GLOBALS['domain'] . $email_for_registration_url . '">' . $GLOBALS['domain'] . $email_for_registration_url . '</td>
                            </tr>
                          </table>
                        </body>
                        </html>
                    ';
                    if (mail($to, $subject, $message, implode("\r\n", $headers))) {
                        return (new PageCustomerFacadeMessagesentmailregistration($mark, null, $email_for_registration_email))->getHTML();
                    } 
                    else {
                        return self::getNotFound($mark);
                    }
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
                    $customerData = $model->getElements(
                        'SELECT * FROM customer WHERE customer_id = ?',
                        [$_SESSION['customer_id']]
                    )[0];
                    $valuta_exchange_rate_id = $model->getElements(
                        'SELECT valuta_exchange_rate_id FROM valuta_exchange_rate WHERE valuta_name = ? ORDER BY valuta_exchange_rate_id DESC LIMIT 1',
                        [$customerData['customer_valuta']]
                    )[0]['valuta_exchange_rate_id'];
                    
                    // $exchangeRateArr = $model->getElements(
                    //     'SELECT * FROM valuta_exchange_rate WHERE valuta_name = ?',
                    //     [$customerData['customer_valuta']]
                    // );


                    // foreach( $exchangeRateArr as $elem ) {
                    //     $exchangeRate = $elem['valuta_exchange_rate'];
                    // }
                    
                    // $isCoinTransaction = !!$model-getElements(
                    //     'SELECT * FROM coin_transaction WHERE coin_transaction_link = ? AND coin_transaction_link_utilized = 0',
                    //     [$coinTransactionLink]
                    // );
                    // do {
                    //     $coinTransactionLink = '/' . $_SESSION['customer_id'] . 'paycoins' . $date; // /1paycoins67464477666 -- такого типа будет ссылка, потом к этой строке будет добавлена
                    //     $isCoinTransaction = $model-getElements(
                    //         'SELECT * FROM coin_transaction WHERE coin_transaction_link = ? AND coin_transaction_link_utilized = 0',
                    //         [$coinTransactionLink]
                    //     );
                    // } while ($isCoinTransaction);
                    $coinsTransactionId = $model->addElements(
                        'INSERT INTO coin_transaction (
                            customer_id, 
                            coin_transaction_date, 
                            coin_transaction_sum, 
                            coin_transaction_status
                            ) VALUE (?, ?, ?, ?)',
                        [$_SESSION['customer_id'], $date, $coins, 'toPaySystem']
                    );
                    $paySysterTransactionId = $model->addElements(
                        'INSERT INTO pay_system_transaction (coin_transaction_id, valuta_exchange_rate_id) VALUE (?, ?)',
                        [$coinsTransactionId, $valuta_exchange_rate_id]
                    );
                    $amount = $coins;
                    $orderNumber = $coinsTransactionId;
                    $returnUrl = $GLOBALS['protocol'] . '://' . $GLOBALS['domain'] .  '/payisgood_' . $coinsTransactionId; // надо строки запихнут в переменные
                    $returnUrlFail = $GLOBALS['protocol'] . '://' . $GLOBALS['domain'] .  '/payisbad_' . $coinsTransactionId; // надо строки запихнуть в переменные
                    /* 
                        я хочу сделать отдельный урл для неудачной оплаты, потому что возможен вариант, когда время ссесси истечет и покупатель, уже не сможет вернуться в свой кабинет и его нужно куда-то выбросить в наружу, но при этом сообщить, что оплата не прошла, чтобы он не офигел. Я такого не делаю при успешной оплате, потому что даже если сессия истечет и человека выброшу из кабинета, он вернувшись в кабинет все равно увидит, что баланс пополнен
                    */
                    $userName = 'somebody'; // надо подставить правильное
                    $password = 123456; // надо подставить правильное
                    $requestBody = [
                        'amount' => $amount,
                        'orderNumber' => $orderNumber, 
                        'returnUrl' => $returnUrl,
                        'failUrl' => $returnUrlFail,
                        'userName' => $userName,
                        'password' => $password
                    ];
                    // $init = curl_init('https://3dsec.sberbank.ru/payment/rest/registerPreAuth.do'); // это реальный адрес, потому нужно будет включить
                    // $init = curl_init('https://3dsec.sberbank.ru/payment/rest/register.do');
                    $init = curl_init('http://sber.loc/registerPreAuth.php'); // надо потом будет удалить

                    curl_setopt($init, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($init, CURLOPT_POST, true);
                    curl_setopt($init, CURLOPT_POSTFIELDS, $requestBody);
                    $json = curl_exec($init);
                    echo $json;
                    $text = json_decode($json, true);
                    if ($text === null) {
                        echo '$text==null';
                        return self::getNotFound($mark);
                    }
                    if (!is_array($text)) {
                        echo '!is_array($text)';
                        return self::getNotFound($mark);
                    }
                    if (!array_key_exists('formUrl', $text) || !array_key_exists('orderId', $text)) {
                        echo 'not formUrl and orderId';
                        $codeError = array_key_exists('errorCode', $text) ? $text['errorCode'] : null; // надо будет потом создать страничку с обработкой ошибок
                        $messageError = array_key_exists('errorMessage', $text) ? $text['errorMessage'] : null; // это на потом, после того как создам страничку для обработки ошибок
                        return self::getNotFound($mark);
                    }
                    $location = $text['formUrl'];
                    $numberOfChanged = $model->updateElements(
                        "UPDATE `pay_system_transaction` SET `pay_system_transaction_order_id` = ? WHERE `coin_transaction_id` = ?",
                        [$text['orderId'], $coinsTransactionId]
                    );
                    if ($numberOfChanged == 1) {
                        header('Location:' . $location);
                        exit;
                    }
                    return self::getNotFound($mark);
                   
                    /* 

                        здесь нужно обратиться к таблице coin_transaction. Указать сумму коинов, указать статус toPaySystem и получить id транзакции
                        далее проводятся манипуляции с платежной системой
                        далее в таблице coin_transaction обновляется статус на, или putOnCoinAccount, или cancelPay
                        и в зависимости от этого, в таблицу customer обновляется сумма располагаемых коинов
                    */
                    // $model->updateCoins($coins, $_SESSION['customer_id'], 'putOnCoinAccount');

                    // return self::returnPageCustomerCabinetPayWithoutStaingId($mark, $model);
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
                // $fromPageArr = ['/dealsdeals', '/dealsdeal', '/treatment', '/bigfile', '/profile', '/profilenotupdated', '/profileupdated', '/history', '/pay', '/payisgood'];
                $fromPageArr = self::$allCustomerCabinetPage;
                return self::routingSimplePayPage($fromPageArr, $cookiesmanagement, $model);
            }
            if ($mark == '/treatment') {
                // $fromPageArr = ['/dealsdeals', '/dealsdeal', '/history', '/profile', '/profilenotupdated', '/profileupdated', '/pay', '/brand', '/payisgood'];
                $fromPageArr = self::$allCustomerCabinetPage;
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
                        return self::getAllparametersPage($model, $cookiesmanagement, $mark, $checkConditionOfReadingDevice, $checkCustomer_order_amount, $checkFileSize);
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
                            $customer_order_data_name_without_underscores = self::replacesUnderscoreWithSpace($customer_order_data_name);
                            $model->addElements(
                                "INSERT INTO customer_order_data (
                                    customer_order_id, 
                                    customer_order_data_name, 
                                    customer_order_data_value
                                    ) VALUES 
                                    (?, ?, ?)",
                                [
                                    $customer_order_id, 
                                    $model->cleanForm($customer_order_data_name_without_underscores), 
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
                    return self::addFile4($date, $customer_order_id, $mark, $model, $email, $coins);
                }
            }
            if ($mark == '/dealsdeals') {
                // $fromPageArr = ['/pay', '/dealsdeal', '/treatment', '/model', '/brand', '/ecu', '/profile', '/profilenotupdated', '/profileupdated', '/history'];
                $fromPageArr = self::$allCustomerCabinetPage;
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
                                "UPDATE customer_order SET customer_order_status = 'paid', customer_order_pay_date = ? WHERE customer_order_id = ?",
                                [$date, $_SESSION['customer_order_id']]
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
                $fromPageArr = self::$allCustomerCabinetPage;
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
                // $fromPageArr = ['/pay', '/dealsdeal', '/treatment', '/profilenotupdated', '/profileupdated', '/history', '/bigfile', '/dealsdeals'];
                $fromPageArr = self::$allCustomerCabinetPage;
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
            if ($mark = $_SERVER['REQUEST_URI'] == '/uploadprovfile') {
                if (self::checkMethodPostAndPageName('/dealsdeal')) {
                    if (array_key_exists('customer_order_id', $_POST)) {
                        return self::uploadTreatedFile($mark, $model, $_POST['customer_order_id']);
                    } 
                }
            }
            if ($mark = $_SERVER['REQUEST_URI'] == '/sentmail') {
                
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
                        $message = '
                            <html>
                            <head>
                            <title>' . self::getText($lang, 'Password recovery', $arrPhrases). '</title>
                            </head>
                            <body>
                            <p>' . self::getText($lang, 'Password recovery', $arrPhrases) . '</p>
                            <table>
                                <tr>
                                <td>' . self::getText($lang, 'We cannot provide you with a link to reset your password, since the email you specified is not registered in our service', $arrPhrases) . '</td>
                                </tr>
                            </table>
                            </body>
                            </html>
                        ';
                    } else {
                        $customer_id = $model->getElements(
                            "SELECT customer_id FROM customer WHERE customer_email = ?",
                            [$_POST['Email']]
                        )[0]['customer_id'];
                        $customer_password_recovery_url = '/' . $date . '_' . $customer_id;
                        $url = $GLOBALS['protocol'] . '://' . $GLOBALS['domain'] . $customer_password_recovery_url;
                        
                        $customer_password_recovery_id = $model->addElements(
                            "INSERT INTO customer_password_recovery (
                                customer_password_recovery_url,
                                customer_password_recovery_date_of_link_creation,
                                customer_id
                                ) VALUES (?, ?, ?)", 
                            [$customer_password_recovery_url,  $date, $customer_id]
                        );
                        $message = '
                            <html>
                            <head>
                            <title>' . self::getText($lang, 'Password recovery', $arrPhrases). '</title>
                            </head>
                            <body>
                            <p>' . self::getText($lang, 'Password recovery', $arrPhrases) . '</p>
                            <table>
                                <tr>
                                <td>' . self::getText($lang, 'In order to recover your password, you need to follow', $arrPhrases) . '<a href="' . $url . '">' . self::getText($lang, 'the link', $arrPhrases) . '</a></td>
                                </tr>
                            </table>
                            </body>
                            </html>
                        ';
                    }
                    if (mail($to, $subject, $message, implode("\r\n", $headers))) {
                        return (new PageCustomerFacadeSentmail($mark, null))->getHTML();
                    } 
                    else {
                        return self::getNotFound($mark);
                    }
                }
                if (self::checkMethodPostAndPageName('/sentmail')) {
                    return (new PageCustomerFacadeSentmail($mark, null))->getHTML();
                }
            }
            if ($alterableURL) {
                if (isset($customerPasswordRecoveryUrl) && $customerPasswordRecoveryUrl) {
                    $mark = $customerPasswordRecoveryUrl;
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
                if (isset($email_for_registration_url) && $email_for_registration_url) {
                    $mark = $email_for_registration_url;
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
                if (isset($paySystemIsGoodUrl) && $paySystemIsGoodUrl) {
                    $mark = $paySystemIsGoodUrl;
                    if ($_SERVER['REQUEST_METHOD'] == "GET") {
                        session_start();
                        // $url = 'https://3dsec.sberbank.ru/payment/rest/getOrderStatusExtended.do'; // это реальный адрес
                        $url = 'http://sber.loc/getOrderStatusExtended.php'; // надо будет удалить
                        $requestBody = [
                            'userName' => $GLOBALS['sberUserName'],
                            'password' => $GLOBALS['sberPass'],
                            'orderNumber' => $paySystemIsGoodId
                        ];
                        if (!$json = self::getJSONfromOuterService($url, $requestBody)) 
                        {
                            echo 'after getJSONfromOuterService';
                            return self::getNotFound($mark); // надо будет в страничку, что-то пошло не так, добавить параметер строку и словарь
                            // здесь ошибка подразумевает, что-то банк отправил и мы это получили, но что-то не то и поэтому непонятно, что произошло. Поэтому нужно чтобы пользователь проверил списались ли у него деньги и если списались нужно, чтобы он с нами связался и мы бы вместе разобрались бы с тем, что случилось. Произошла ошибка на стороне банка, Вам нужно связаться с нами.
                        }
                        // var_dump($json);
                        // echo '<br>';
                        $text = json_decode($json, true);
                        // echo json_last_error(), '<br>'; 
                        if (json_last_error() !== 0) {
                            // echo 'json_last_error', '<br>';
                            return self::getNotFound($mark); // надо будет передать ошибку может быть поможет
                        }
                        $actionCode = array_key_exists('actionCode', $text) ? $text['actionCode'] : null;
                        $orderStatus = array_key_exists('orderStatus', $text) ? $text['orderStatus'] : null;
                        $orderNumber = array_key_exists('orderNumber', $text) ? $text['orderNumber'] : null;

                        if ($orderNumber && $actionCode === 0 && $orderStatus === 1) { // надо наверное числовые коды вынести в переменные, чтобы можно было менять из одного места
                            $numberNotactiveLine = $model->updateElements(
                                "UPDATE `pay_system_transaction` SET `pay_system_transaction_notactivelink` = 1 WHERE `coin_transaction_id` = ?",
                                [$paySystemIsGoodId]
                            );
                            $numberChangeStatusLine = $model->updateElements(
                                "UPDATE `coin_transaction` SET `coin_transaction_status` = ? WHERE `coin_transaction_id` = ?",
                                ['PaySystemOK', $paySystemIsGoodId]
                            );
                            if ($numberNotactiveLine && $numberChangeStatusLine) {
                                $customerArr = $model->getElements(
                                    'SELECT * FROM coin_transaction WHERE coin_transaction_id = ?',
                                    [$paySystemIsGoodId]
                                )[0];
                                if ($customerArr) {
                                    $numberCoinsLine = $model->updateElements(
                                        "UPDATE `customer` SET `customer_coins` = `customer_coins` + ? WHERE `customer_id` = ?",
                                        [$customerArr['coin_transaction_sum'], $customerArr['customer_id']]
                                    );
                                    if ($numberCoinsLine != 1) {
                                        echo 'Из таблицы customer вернулось больше одно строки или ни одной';
                                        return self::getNotFound($mark);
                                    }
                                } else {
                                    echo 'Если из таблицы coin_transaction ничего не вернулось';
                                    return self::getNotFound($mark);
                                }
                            } else {
                                echo 'в таблице pay_system_transaction ничего не обновилось';
                                return self::getNotFound($mark);
                            }
                        } else {
                            $numberOfLines = $model->updateElements(
                                "UPDATE `coin_transaction` SET `coin_transaction_status` = ? WHERE `coin_transaction_id` = ?", 
                                [ 'PaySystemFailed', $paySystemIsGoodId]
                            );
                            echo 'cтатусные переменные в не соответсвуют проведению оплаты';
                            return self::getNotFound($mark);
                        }

                        if (isset($_SESSION['customer_id'])) {
                            $customerData = $model->getElements(
                                'SELECT * FROM customer WHERE customer_id = ?',
                                [$_SESSION['customer_id']]
                            )[0];
                            return (new PageCustomerCabinetPayisgood($mark, $_SESSION['customer_id'], $customerData['customer_email'], $customerData['customer_coins']))->getHTML();
                        }
                        return (new PageCustomerFacadeIndex($mark, null))->getHTML();
                    }
                    echo 'неправильная ссылка';
                    return self::getNotFound($mark);
                }
                if (isset($paySystemIsBadUrl) && $paySystemIsBadUrl) {
                    $mark = $paySystemIsBadUrl;
                    if ($_SERVER['REQUEST_METHOD'] == "GET") {
                        $numberOfLines = $model->updateElements(
                            "UPDATE `coin_transaction` SET `coin_transaction_status` = ? WHERE `coin_transaction_id` = ?", 
                            [ 'PaySystemFailed', $paySystemIsBadId]
                        );
                        if ($numberOfLines === 1) {
                            session_start();
                            if (isset($_SESSION['customer_id'])) {
                                $customerData = $model->getElements(
                                    'SELECT * FROM customer WHERE customer_id = ?',
                                    [$_SESSION['customer_id']]
                                )[0];
                                return (new PageCustomerCabinetPayisbad($mark, $_SESSION['customer_id'], $customerData['customer_email'], $customerData['customer_coins']))->getHTML();
                            }
                            return (new PageCustomerFacadePayisbadF($mark, null))->getHTML();
                        }
                    }
                    return self::getNotFound($mark);
                }
            }

            // это раздел для провайдера
            if ($_SERVER['REQUEST_URI'] == '/maingate') {
                $mark = '/maingate';
                if (self::checkMethodPostAndPageNameAndReferer('/maingate', $checkReferer)) {
                    return '"maingage Page" must be here'; //Это нужно было для проверки, можно удалять
                }
                if ($_SERVER['REQUEST_METHOD'] == "GET") {

                    return "<form method='POST' action=''/maingate''><input type='hidden' value='/maingate' name='Page'><input type='submit'></form>"; //это нужно было для проверки можно удалять
                    
                }
                return self::getNotFound($mark);
            } // это не закончено

            if ($_SERVER['REQUEST_URI'] == '/gate') {
                $mark == '/gate';
                if ($_SERVER['REQUEST_METHOD'] == "GET") {
                    return (new PageProviderGate($mark))->getHTML();
                }
                return (new PageCustomerFacadeNotfound($mark, null))->getHTML();
            }

            if ($_SERVER['REQUEST_URI'] == '/admin') {
                $mark = '/admin';
                foreach(self::$allProviderPage as $page) {
                    if (self::checkMethodPostAndPageNameAndReferer($page, $checkReferer)) {
                        if ($page == '/gate') {
                            $provider = $model->getElements(
                                "SELECT * FROM provider WHERE provider_login = ?",
                                [$_POST['login']]
                            );
                            if ($provider && password_verify($_POST['pass'], $provider[0]['provider_password'])) {
                                session_start();
    
                                $_SESSION['provider_id'] = $provider[0]['provider_id'];
                                return self::getAdminPage($mark, $model, $_SESSION['provider_id']);
                            }
                            return (new PageProviderGate($mark, true))->getHTML();
                        }
                        return self::getAdminPageWithSession($mark, $model);
                    }
                }
            }

            if (($mark = $_SERVER['REQUEST_URI']) == '/downloadprovfile') {
                if (self::checkMethodPostAndPageNameAndReferer('/deal', $checkReferer)) {
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    if (self::checkExistPosts(['customer_order_id']) && isset($_FILES['treatedfile'])){
                        $customerId = $model->getElements(
                            'SELECT * FROM customer_order WHERE customer_order_id = ?',
                            [$_POST['customer_order_id']]
                        )[0]['customer_id'];
                        $dirPathWithoutRootPath = $GLOBALS['outgoingFileDir'] . '/' . $customerId . '/' . $_POST['customer_order_id'];
                        $dirPath = $GLOBALS['saveFilePath'] . $dirPathWithoutRootPath;
                        if (!is_dir($dirPath)) {
                            if (!mkdir($dirPath, $GLOBALS['accessRight'], true)) {
                                return 'не получилось создать директорию';
                            }
                        }
                        $file_extension = strrchr($_FILES['treatedfile']['name'], '.');
                        $fileName = $customerId . '_' . $_POST['customer_order_id']  . '_' . $date . '_treated' . $file_extension;
                        $fileNameWithSlash = "/" . $fileName;
                        $path_to_file = $dirPath . $fileNameWithSlash;
                        $path_to_file_WithoutRootPath = $dirPathWithoutRootPath . $fileNameWithSlash;
                        if ($_FILES['treatedfile']['size'] < $GLOBALS['fileSizeFromCustomer']) {
                            if (move_uploaded_file($_FILES['treatedfile']['tmp_name'],  $path_to_file)) {
                                $open_resurs = fopen($path_to_file, 'rb');
                                $file_is_string = fread($open_resurs, $GLOBALS['fileSizeFromCustomer']);
                                if (customCRC16($file_is_string) == $_POST['checksumtreatedfile']) {
                                    $isFilePath = $model->getElements(
                                        "SELECT * FROM file_path WHERE customer_order_id = ? AND file_path_what_file = ?",
                                        [$_POST['customer_order_id'], $GLOBALS['treatmentedFile']]
                                    );
                                    if (count($isFilePath) > 1) {
                                        return "В базе зафиксировано несколько путей сохранения файлов для данной сделки";
                                    }
                                    if (!$isFilePath) {
                                        $updatedCount = $model->addElements(
                                            "INSERT INTO file_path (customer_order_id, file_path_date, file_path_path, file_path_what_file, file_path_checksum) VALUES (?, ?, ?, ?, ?)",
                                            [$_POST['customer_order_id'], $date, $path_to_file_WithoutRootPath, $GLOBALS['treatmentedFile'], $_POST['checksumtreatedfile']]
                                        );
                                    } else {
                                        $oldFilePath = $GLOBALS['saveFilePath'] . $isFilePath[0]['file_path_path'];
                                        if (!unlink($oldFilePath)) {
                                            return 'не получилось удалить старый файл, но новый файл создан, но путь в базе остался старый';
                                        }
                                        $updatedCount = $model->updateElements(
                                            "UPDATE file_path SET file_path_date = ?, file_path_path = ? WHERE customer_order_id = ? AND file_path_what_file = ?",
                                            [$date, $path_to_file_WithoutRootPath, $_POST['customer_order_id'], $GLOBALS['treatmentedFile']]
                                        );
                                    }
                                    if (!$updatedCount) {
                                        return 'Не получилось записать в базу данный путь до файла';
                                    }
                                    if ($_FILES['treatedfile']['error'] === 0) {
                                        return self::getDealPage($mark, $model, 'Файл загружен');
                                    }
                                    return 'Что-то пошло не так $_FILES[treatedfile][error] не равен 0';
                                } else {
                                    unlink($path_to_file);
                                    rmdir($dirPath);
                                    return 'Чек суммы не совпали. файл и директории удалены. Надо попробовать еще раз'; // над сделать страницу
                                }
                            } else {
                                $errorCode = $_FILES['treatedfile']['error'];
                                if ($errorCode === 1 || $errorCode === 2) {
                                    return self::getDealPage($mark, $model, 'Файл слишком большой');
                                }
                                if ($errorCode === 4) {
                                    return self::getDealPage($mark, $model, 'Файл не был загружен');
                                }
                                if ($errorCode === 3) {
                                    return self::getDealPage($mark, $model, 'Файл загружен частично');
                                }
                                if ($errorCode === 7) {
                                    return self::getDealPage($mark, $model, 'Не удалось записать файл на диск');
                                }
                                if ($errorCode === 6) {
                                    return self::getDealPage($mark, $model, 'Отсутствует временная папка');
                                }
                                if ($errorCode === 8) {
                                    return self::getDealPage($mark, $model, 'При загрузке файла что-то пошло не так, но что именно не ясно');
                                }
                                return self::getNotFound($mark);
                            }
                        } else {
                            return self::getDealPage($mark, $model, 'Файл слишком большой');
                        }
                    }
                }
            }

            if ($mark = $_SERVER['REQUEST_URI'] == '/uploadcustfile') {
                if (self::checkMethodPostAndPageNameAndReferer('/deal', $checkReferer)) {
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    if (self::checkExistPosts(['link-download'])){
                        return self::uploadFile($mark, $model, $_POST['link-download']);
                    }
                }
            }

            if ($mark = $_SERVER['REQUEST_URI'] == '/deals') {

                if (self::checkMethodPostAndPageName('/deal') || self::checkMethodPostAndPageName('/admin')) {
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    return (new PageProviderDeals($mark, [], []))->getHTML();
                }

                if (self::checkMethodPostAndPageName('/deals')) {
                    
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    
                    if (self::checkExistPosts(['datestart', 'dateend', 'customer_id'])) {
                        
                        $sqlRequest['request'] = "SELECT * FROM customer_order WHERE provider_id = ? ";
                        $sqlRequest['value'] = [$_SESSION['provider_id']];

                        // это нужно, потому что в данном блоке if класс PageProviderDeals ждет не пустой массив messages 
                        $messages['empty_message'] = [
                            'flag' => false,
                            'text' => ''
                        ]; 
                        // end

                        if ($_POST['datestart'] || $_POST['dateend']) {
                            $messages['no_datestart'] = [
                                'flag' => !$_POST['datestart'], 
                                'text' => 'Не устанолена начальная дата'
                            ];

                            $messages['no_dateend'] = [
                                'flag' => !$_POST['dateend'],
                                'text' => 'Не установлена конечная дата'
                            ];

                            if ($_POST['datestart'] && $_POST['dateend']) {
                                $datestartArr = date_parse($_POST['datestart']);
                                $dateendArr = date_parse($_POST['dateend']);
                                $dateendArr['day']++; // дело в том, что день начинается с 0 секунды, поэтому все что после в дату не попадают, для этого увеличиаем на 1 день
                                $messages['datestart_notcorrect'] = [
                                    'flag' => !checkdate($datestartArr['month'], $datestartArr['day'], $datestartArr['year']),
                                    'text' => 'Начальная дата некорректная'
                                ];

                                $messges['dateend_notcorrect'] = [
                                    'flag' => !checkdate($dateendArr['year'], $dateendArr['month'], $dateendArr['day']),
                                    'text' => 'Конечная дата некорректная'   
                                ];
                                $dateend = mktime(0, 0, 0, $dateendArr['month'], $dateendArr['day'], $dateendArr['year']);
                                $datestart = mktime(0, 0, 0, $datestartArr['month'], $datestartArr['day'], $datestartArr['year']);
                                $messages['dateend_less_then_datestart'] = [
                                    'flag' => $dateend < $datestart,
                                    'text' => 'Конечная дата более ранняя чем начальная'   
                                ];
                                $sqlRequest['request'] .= " AND customer_order_date >= ? AND customer_order_date <= ? ";
                                $sqlRequest['value'][] = $datestart;
                                $sqlRequest['value'][] = $dateend;
                             }
                        }
                        if ($_POST['customer_id']) {
                            $sqlRequest['request'] .= " AND customer_id = ?  ";
                            $sqlRequest['value'][] = $_POST['customer_id'];
                        }
                        if (isset($_POST['customer_order_status']) && $_POST['customer_order_status']) {
                            foreach ( $_POST['customer_order_status'] as $customer_order_status) {
                                $sqlRequest['request'] .= " AND customer_order_status = ?  ";
                                $sqlRequest['value'][] = $customer_order_status;
                            }
                        }
                        if (isset($messages)) {
                            foreach ($messages as $message) {
                                if ($message['flag'] == true) {
                                    $isMessage = true;
                                    break;
                                }
                                $isMessage = false;
                            }
                        } else {
                            $isMessage = false;
                        }
                        if ($isMessage) {
                            $parameters = [];
                        } else {
                            $parameters = $model->getElements(
                                $sqlRequest['request'],
                                $sqlRequest['value']
                            );
                        }
                        return (new PageProviderDeals($mark, $parameters, $messages))->getHTML();
                    }
                    if (self::checkExistPosts(['all_deals'])) {
                        $parameters = $model->getElements(
                            "SELECT * FROM customer_order WHERE provider_id = ?",
                            [$_SESSION['provider_id']]
                        );
                        return (new PageProviderDeals($mark, $parameters))->getHTML();
                    }
                    return (new PageProviderDeals($mark, [], []))->getHTML();
                }

                if (self::checkMethodPostAndPageName('/admin')) {
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    $parameters = $model->getAllDeals($receiveBD);
                    return (new PageProviderDeals($mark, $parameters))->getHTML();
                }
                if (self::checkMethodPostAndPageName('/valuta')) {
                    if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                        return $gettingPage;
                    }
                    $parameters = $model->getAllDeals($receiveBD);
                    return (new PageProviderDeals($mark, $parameters))->getHTML();
                }
                
            }

            if ($_SERVER['REQUEST_URI'] == '/deal') {
                $mark = '/deal';
                foreach (self::$allProviderPage as $page) {
                    if (self::checkMethodPostAndPageNameAndReferer($page, $checkReferer)) {
                        if ($page == '/deal') {

                            // отправить новое сообщение
                            if (self::checkExistPosts(['message_content', 'customer_order_id'])) {
                                return self::getDealPageWithSession($mark, $model, true);
                            }
                            // ---

                            //пометить сообщение как прочитанное 
                            if (self::checkExistPosts(['message_id', 'customer_order_id', 'button_message_seen'])) {
                                if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                                    return $gettingPage;
                                }
                                $isItUpdated = $model->updateElements(
                                    "UPDATE message SET message_seen = 1 WHERE message_id = ?",
                                    [$_POST['message_id']]
                                );
                                if ($isItUpdated) {
                                    return self::getDealPage($mark, $model);
                                }
                            }
                            // ---

                            // изменение статуса сделки
                            if (self::checkExistPosts(['customer_order_id', 'customer_order_status'])) {
                                if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                                    return $gettingPage;
                                }
                                $status = $model->getElements(
                                    "SELECT customer_order_status FROM customer_order WHERE customer_order_id = ?",
                                    [$_POST['customer_order_id']]
                                )[0]['customer_order_status'];
                                if ($status == $_POST['customer_order_status']) {
                                    return self::getDealPage($mark, $model);
                                }
                                $isItUpdated = $model->updateElements(
                                    "UPDATE customer_order SET customer_order_status = ? WHERE customer_order_id = ?",
                                    [$_POST['customer_order_status'], $_POST['customer_order_id']]
                                );
                                if ($isItUpdated) {
                                    return self::getDealPage($mark, $model, '', null); // надо проверить нужны ли третий и четвертый аргументы
                                }
                            }
                            // ---

                            if (self::checkExistPosts(['customer_order_id', 'link-download'])){
                                return self::getDealPageWithSession($mark, $model, false);
                            }

                            // присвоение сделки сотруднику
                            if (self::checkExistPosts(['provider_id', 'customer_order_id'])) { 
                                if (($gettingPage = self::startSessionAndCheckPassForProv($mark)) !== null) {
                                    return $gettingPage;
                                }
                                $isItUpdated = $model->updateElements(
                                    "UPDATE customer_order SET provider_id = ? WHERE customer_order_id = ?",
                                    [$_POST['provider_id'], $_POST['customer_order_id']]
                                );
                                if ($isItUpdated) {
                                    return self::getDealPage($mark, $model, '', null);
                                }
                            }
                            // ---

                        } else {
                            if (self::checkExistPosts(['customer_order_id'])) {
                                return self::getDealPageWithSession($mark, $model, false);
                            }
                        }
                    }
                }
            }            
            return self::getNotFound($mark); // это последний бастион если ни одно условие не совпало

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    static function printPage()
    {
        echo self::getPage();
    }
}