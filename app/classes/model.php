<?php
class Model 
{
    function cleaningDataForm($data) { // эта функция для очистки лишнего и безопасности html и js
        return htmlentities(trim($data));
    }
    function cleanForm($data_form ) { // Эта функция очищает для защиты от SQL-иньекций
        return strtr($data_form, ['_' => '\_', '%' => '\%']);
    }
    function appDB() {
        $dbhost = $GLOBALS['dbhost'];
        $dbname = $GLOBALS['dbname'];
        $dbadmin = $GLOBALS['dbadmin'];
        $dbadminpass = $GLOBALS['dbadminpass'];
        try {
            $dns = new PDO ("mysql:host=$dbhost;dbname=$dbname", $dbadmin, $dbadminpass);
            $dns->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dns;
        } catch (PDOException $e) {
            print "Couldn't connect to data base: " . $e->getMessage();
        }
    }
    function getValuta(PDO $appDB)
    {
        $appDB->beginTransaction();
        $require = $appDB->prepare("SELECT * FROM valuta_exchange_rate");
        $exec = $require->execute([]);
        if ($exec === false) {
            $appDB->commit();
            return false;
        }
        $elementArr = [];
        while ($string = $require->fetch(PDO::FETCH_ASSOC)) {
            $elementArr[] = $string;
        }
        $appDB->commit();
        return $elementArr;
    }
    function getOneValutaValue(PDO $appDB, $name)
    {
        $appDB->beginTransaction();
        $require = $appDB->prepare("SELECT valuta_exchange_rate FROM valuta_exchange_rate WHERE valuta_name = '$name'");
        $exec = $require->execute([]);
        if ($exec === false) {
            $appDB->commit();
            return false;
        }
        $elementArr = [];
        while ($string = $require->fetch(PDO::FETCH_ASSOC)) {
            $elementArr[] = $string;
        }
        $appDB->commit();
        if (count($elementArr) <= 0) {
            return false;
        }
        if (count($elementArr) > 1) {
            throw new Exception('Ошибка в базе данных. Значения валюты представлены несколько раз');
        }
        return $elementArr[0]['valuta_exchange_rate'];
    }
    function updateValuta($appDB, $exchangeRate, $name)
    {
        $appDB->beginTransaction();
        $require = $appDB->exec("UPDATE `valuta_exchange_rate` SET `valuta_exchange_rate` = '$exchangeRate' WHERE `valuta_name` = '$name'");
        if ($require === false) {
            $appDB->commit();
            return false;
        }
        $appDB->commit();
        return $require;
    }
    function addValuta($appDB, $exchangeRate, $name) 
    {
        $parameterArr = [
            'valuta_name' => $name, 
            'valuta_exchange_rate' => $exchangeRate
        ];
        return $this->addElement($appDB, 'valuta_exchange_rate', $parameterArr);
    }
    function getElementsByOneParameter(PDO $appDB, string $element, string $from, string $where, $value)
    {
        $appDB->beginTransaction();
        $require = $appDB->prepare("SELECT $element FROM $from WHERE $where = ?");
        $exec = $require->execute([$value]);
        if ($exec === false) {
            $appDB->commit();
            return false;
        }
        $elementArr = [];
        while ($string = $require->fetch()) {
            $elementArr[] = $string;
        }
        $appDB->commit();
        return $elementArr;
    }
    
    function getElementsByOneParameterWithNameArr(PDO $appDB, $selectList, $from, $where, $whereValue, $order) : array
    {
        $appDB->beginTransaction();
        $require = $appDB->prepare("SELECT $selectList FROM $from WHERE $where = ? ORDER BY $order");
        $exec = $require->execute([$whereValue]);
        if ($exec === false) {
            $appDB->commit();
            return false;
        }
        $elementArr = [];
        while ($string = $require->fetch(PDO::FETCH_ASSOC)) {
            $elementArr[] = $string;
        }
        $appDB->commit();
        return $elementArr;
    }
    // function getIdItemOrderListByIdOreder(PDO $appDB, $idOrder)
    // {
    //     $appDB->beginTransaction();
    //     $require = $appDB->prepare("SELECT order_item_id FROM order_items WHERE order_id = ?");
    //     $exec = $require->execute([$idOrder]);
    //     if ($exec === false) {
    //         $appDB->commit();
    //         return false;
    //     }
    //     $elementArr = [];
    //     while ($string = $require->fetch(PDO::FETCH_ASSOC)) {
    //         $elementArr[] = $string['order_item_id'];
    //     }
    //     $appDB->commit();
    //     return $elementArr;
    // }
    function getTimeIdStatusAmountOrdersByIdOrder($appDB, $order_id)
    {
        return $this->getElementsByOneParameterWithNameArr($appDB, 'order_date, order_status, order_amount', 'orders', 'order_id', $order_id, 'order_id')[0];
    }
    function getAllOrdersByIdOrder($appDB, $order_id)
    {
        return $this->getElementsByOneParameterWithNameArr($appDB, '*', 'orders', 'order_id', $order_id, 'order_id')[0];
    }
    function getAllCustomerByIdCustomer($appDB, $customer_id)
    {
        return $this->getElementsByOneParameterWithNameArr($appDB, '*', 'customers', 'customer_id', $customer_id, 'customer_id')[0];
    }
    function getTimeIdStatusAmountOrdersByIdCustomer(PDO $appDB, string $id_customer) : array
    {
        return $this->getElementsByOneParameterWithNameArr($appDB, 'order_id, order_date, order_status, order_amount', 'orders', 'customer_id', $id_customer, 'order_id');
    }
    // function getAllFileprocessingByIdOrderitem2(PDO $appDB, string $order_item_id)
    // {
    //     $appDB->beginTransaction();
    //     $require = $appDB->prepare("SELECT * FROM file_processing WHERE order_item_id = ?");
    //     $exec = $require->execute([$order_item_id]);
    //     if ($exec === false) {
    //         $appDB->commit();
    //         return false;
    //     }
    //     $elementArr = [];
    //     while ($string = $require->fetch(PDO::FETCH_ASSOC)) {
    //         $elementArr[] = $string;
    //     }
    //     $appDB->commit();
    //     return $elementArr[0]; // возвращаем первый элемент массива, потому что вернуться должен только один элемент массива
    // }
    function getAllFileprocessingByIdOrderitem(PDO $appDB, string $order_item_id) 
    {
        return $this->getElementsByOneParameterWithNameArr($appDB, '*', 'file_processing', 'order_item_id', $order_item_id, 'order_item_id')[0];
    }
    function getAllFileprocessingCarByIdOrderitem(PDO $appDB, string $order_item_id)
    {
        return $this->getElementsByOneParameterWithNameArr($appDB, '*', 'file_processing_car', 'order_item_id', $order_item_id, 'order_item_id')[0];
    }
    function getAllMessagesByIdOrderitem(PDO $appDB, string $order_item_id)
    {
        return $this->getElementsByOneParameterWithNameArr($appDB, '*', 'messages', 'order_item_id', $order_item_id, 'message_date');
    }
    function getOrderItemIdByOrderId(PDO $appDB, string $order_id)
    {
        $arrInArr = $this->getElementsByOneParameterWithNameArr($appDB, 'order_item_id', 'order_items', 'order_id', $order_id, 'order_item_id');
        $arr = [];
        foreach ($arrInArr as $elem) {
            $arr[] = $elem['order_item_id'];
        }
        return $arr;
    }
    function getElementByUniqueParameter(PDO $appDB, string $element, string $from, string $where, $value)
    {
        $elementList = $this->getElementsByOneParameter($appDB, $element, $from, $where, $value);
        $elementNumber = count($elementList);
        if ($elementNumber === 0) {
            return null;
        }
        if ($elementNumber !== 1) {
            return false;
        }
        return $elementList[0][0];
    }
    function getCustomerElementByLogin(PDO $appDB, $loginValue, string $element) {
        return $this->getElementByUniqueParameter($appDB, $element, 'customers', 'customer_login', $loginValue);
    }
    function getCustomerElementByLoginPass1 (PDO $appDB, $loginValue, $passValue, string $element) {
        $hash = $this->getCustomerElementByLogin($appDB, $loginValue, 'customer_password');
        
        if (password_verify($passValue, $hash)) {
            return $this->getCustomerElementByLogin($appDB, $loginValue, $element);
        }
        return false;
    }
    function getCustomerElementById(PDO $appDB, string $element, int $valueId)
    {
        return $this->getElementByUniqueParameter($appDB, $element, 'customer', 'customer_id', $valueId);
    }
    function updateElementsByOneParameter($appDB,  $tableName, $elementName, $elementValue, $parameterName, $parameterValue )
    {
        $appDB->beginTransaction();
        $require = $appDB->exec("UPDATE $tableName SET $elementName = '$elementValue' WHERE $parameterName = '$parameterValue'");
        if ($require === false) {
            $appDB->commit();
            return false;
        }
        $appDB->commit();
        return $require;
    }
    function updateElementByUniqueParameter($appDB,  $tableName, $elementName, $elementValue, $parameterName, $parameterValue)
    {
        $require = $this->updateElementsByOneParameter($appDB,  $tableName, $elementName, $elementValue, $parameterName, $parameterValue);
        if ($require > 1) {
            return false;
        }
        return $require;
    }
    function updateCustomerElementById($appDB, $elementName, $elementValue, $parameterValue)
    {
        return $this->updateElementByUniqueParameter($appDB,  'customers', $elementName, $elementValue, 'customer_id', $parameterValue);
    }
    function updateCoins1(PDO $appDB, $addingCoins, int $valueId)
    {
        if (is_numeric($addingCoins)) {
            $this->addElement($appDB, 'buy_coins', [
                'customer_id' => $valueId,
                'buy_coins_date' => time(),
                'buy_coins_sum' => $addingCoins
            ]);
            return  $this->updateCustomerElementById($appDB, 'customer_coins', $addingCoins + $this->getCustomerElementById($appDB, 'customer_coins', $valueId), $valueId);
        }
        print (new PageCustomerFacadeNotfound('Notfound'))->getHTML();
            exit;
    }
    function checkLogin($appDB, $loginValue)
    {
        return $this->getCustomerElementByLogin($appDB, $loginValue, 'customer_login');
    }
    function checkLogin1 ($login) {
        $getPassword = $this->appDB()->prepare("SELECT customers.customer_login FROM customers WHERE customers.customer_login = ?");
        $getPassword->execute([$login]);
        if ($getPassword === false) {
            $error = $appDB->errorInfo();
            print "Ошибки: " . $error[0] . $error[1] . $error[2]; // надо будет строку покрасивеее оформить
            return false;
        }
        return isset($getPassword->fetch()['customer_login']);
    }
    function checkPass1 ($appDB, $loginValue, $passValue)
    {
        $hash = $this->getCustomerElementByLogin($appDB, $loginValue, 'customer_password');
        return password_verify($passValue, $hash);
    }
    function addElement(PDO $appDB, string $table, array $parameterArr)
    {
        $arr = [];
        $parameterList = '';
        $valueList = '';
        $lastKey = array_key_last($parameterArr);
        foreach($parameterArr as $parameter => $value) {
            $arr[] = $value;
            if ($parameter == $lastKey) {
                $parameterList .= $parameter; 
                $valueList .= '?';
            } else {
                $parameterList .= "$parameter, ";
                $valueList .= '?, ';
            }
        }
        // // это удалить
        // echo '<div>';
        // echo '<pre>';
        // print_r($arr);
        // echo $parameterList . '<br>';
        // echo $valueList . '<br>';
        // echo '</pre>';
        // echo '</div>';
        // // конец это удалить
        try {
            $appDB->beginTransaction();
            $result = $appDB->prepare("INSERT INTO $table ($parameterList) VALUES ($valueList)");
            $addCust = $result->execute($arr);
            $lastId = $appDB->lastInsertId();
            $appDB->commit();
            return $lastId;
        } catch (PDOException $e) {
            // print (new PageCustomerFacadeNotfound('Notfound'))->getHTML();
            // exit;
            echo $e->getMessage();
            // echo 'herna<br>';
            $appDB->commit();
        }
    }
    function addOrder(
        PDO $appDB,
        $customer_id,
        $order_amount,
        $date,
        $order_status,
        $message_content,
        $message_from,
        $message_seen,
        $vehicle_type,
        $vehicle_brand,
        $vehicle_model,
        $selected_ecu,
        $plate_of_vehicle,
        $vin_vehicle_identification_number,
        $reading_device,
        $path_to_file, 
        array $parameterVehicleTypeArr
        )
    {
        $parameterArrOrders = [
            'customer_id' => $customer_id,
            'order_amount' => $order_amount,
            'order_date' => $date,
            'order_status' => $order_status
        ];
        $order_id = $this->addElement($appDB, 'orders', $parameterArrOrders);
        // $order_id = $this->getElementByUniqueParameter($appDB, 'order_id', 'orders', 'customer_id', $customer_id);
        $parameterArrOrderitem = [
            'order_id' => $order_id
        ];
        $order_item_id = $this->addElement($appDB, 'order_items', $parameterArrOrderitem);
        // $order_item_id = $this->getElementByUniqueParameter($appDB, 'order_item_id', 'order_items', 'order_id', $order_id);
        $parameterArrFileprocessing = [
            'order_item_id' => $order_item_id,
            'file_processing_vehicle_type' => $vehicle_type,
            'file_processing_vehicle_brand' => $vehicle_brand,
            'file_processing_vehicle_model' => $vehicle_model,
            'file_processing_selected_ecu' => $selected_ecu,
            'file_processing_plate_of_vehicle' => $plate_of_vehicle,
            'file_processing_vin_vehicle_identification_number' => $vin_vehicle_identification_number,
            'file_processing_reading_device' => $reading_device,
            'file_processing_path_to_file' => $path_to_file
        ];
        $this->addElement($appDB, 'file_processing', $parameterArrFileprocessing);
        if ($message_content) {
            $parameterArrMessages = [
                'order_item_id' => $order_item_id,
                'message_content' => $message_content,
                'message_from' => $message_from,
                'message_date' => $date,
                'message_seen' => $message_seen
            ];
            $message_id = $this->addElement($appDB, 'messages', $parameterArrMessages);
        } else {
            $message_id = null;
        }
        $parameterVehicleTypeArr['order_item_id'] = $order_item_id; 
        $tableNameVehicleTypePrefix =  'file_processing_';
        $this->addElement($appDB, $tableNameVehicleTypePrefix . strtolower($vehicle_type), $parameterVehicleTypeArr);
        return [
            'order_item_id' => $order_item_id,
            'order_id' => $order_id,
            'message_id' => $message_id
        ];
    }
    function addCustomer($tel, $email, $valuta, $password, $lang)
    {
        $appDB = $this->appDB();
        $parameterArr = [
            'customer_email' => $this->cleanForm($email),
            'customer_telephone' => $this->cleanForm($tel),
            'customer_valuta' => $this->cleanForm($valuta),
            'customer_language' => $this->cleanForm($lang), 
            'customer_registration_date' => time(),
            'customer_password' => $this->hashPass($this->cleanForm($password))
        ];
        $this->addElement($appDB, 'customer', $parameterArr);
    }
    function hashPass($pass)
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }
    function getNotSeenMessagesOrderIdFrom($appDB, $from)
    {
        $appDB->beginTransaction();
        $require = $appDB->prepare("SELECT DISTINCT o.order_id FROM order_items o INNER JOIN messages m ON o.order_item_id = m.order_item_id WHERE m.message_seen = 0 AND m.message_from = ?");
        $exec = $require->execute([$from]);
        if ($exec === false) {
            $appDB->commit();
            return false;
        }
        $elementArr = [];
        while ($string = $require->fetch(PDO::FETCH_ASSOC)) {
            $elementArr[] = $string;
        }
        $appDB->commit();
        return $elementArr;
    }
    function getAllDeals($appDB)
    {
        $appDB->beginTransaction();
        $require = $appDB->prepare('SELECT * FROM orders INNER JOIN customers ON orders.customer_id = customers.customer_id');
        $exec = $require->execute([]);
        if ($exec === false) {
            $appDB->commit();
            return false;
        }
        $elementArr = [];
        while($string = $require->fetch(PDO::FETCH_ASSOC)) {
            $elementArr[] = $string;
        }
        $appDB->commit();
        return $elementArr;
    }


    // новые методы 
    function getElements(string $query, array $parameters)
    {
        try {
            $appDB = $this->appDB();
            $appDB->beginTransaction();
            $require = $appDB->prepare($query);
            $exec = $require->execute($parameters);
            if ($exec === false) {
                $appDB->commit();
                return false;
            }
            $elementArr = [];
            while($string = $require->fetch(PDO::FETCH_ASSOC)) {
                $elementArr[] = $string;
            }
            $appDB->commit();
            return $elementArr;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $appDB->commit();
        }
    }
    function addElements(string $query, array $parameters)
    {
        try {
            $appDB = $this->appDB();
            $appDB->beginTransaction();
            $result = $appDB->prepare($query);
            $addCust = $result->execute($parameters);
            $lastId = $appDB->lastInsertId();
            $appDB->commit();
            return $lastId;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $appDB->commit();
        }
    }
    function updateElements($query, $parameters)
    {
        try {
            $appDB = $this->appDB();
            $appDB->beginTransaction();
            $result = $appDB->prepare($query);
            $exec = $result->execute($parameters);
            $appDB->commit();
            return $exec; // возвращает количество модифицированных строк
        } catch (PDOException $e) {
            echo $e->getMessage();
            $appDB->commit();
        }
    }
    function checkEmail($email)
    {
        $query = $this->getElements(
            "SELECT customer_id  FROM customer WHERE customer_email = ?",
            [$email]
        );
        return !!$query;
    }
    function addConditionValueService() /* это экспериментальная функция, надо будет переделать */
    {
        $condition_id_id = $this->addElements(
            "INSERT INTO condition_id (condition_id_works) VALUES(1)", []
        );
        $this->addElements(
            "INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
                $condition_id_id,
                'vehicle type',
                'morine',
                (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
            )", []
        );
        $this->addElements(
            "INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
                $condition_id_id,
                'vehicle brand',
                'morina3',
                (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
            )", []
        );
        $this->addElements(
            "INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
                $condition_id_id,
                'vehicle model',
                'somethingMorine3',
                (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
            )", []
        );
        $this->addElements(
            "INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
                $condition_id_id,
                'ecu',
                '2ggg444',
                (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
            )", []
        );
        $this->addElements(
            "INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
                $condition_id_id,
                (SELECT service_id FROM service WHERE service_name = 'egr off'),
                200
            )", []
        );
        $this->addElements(
            "INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
                $condition_id_id,
                (SELECT service_id FROM service WHERE service_name = 'cat off'),
                100
            )", []
        );
        $this->addElements(
            "INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
                $condition_id_id,
                (SELECT service_id FROM service WHERE service_name = 'checksum correction'),
                20
            )", []
        );
    }
    // function checkPass ($email, $pass)
    // {
    //     if ($this->checkEmail($email)) {
    //         $hash = getElements(
    //             'SELECT customer_password FROM customer WHERE customer_email = ?', [$email]
    //         );
    //         return password_verify($pass, $hash);
    //     }
    //     return false;
    // }
    function checkPass($email, $pass)
    {
        $hashArr = $this->getElements(
            'SELECT customer_password FROM customer WHERE customer_email = ?',
            [$email]
        );
        if (count($hashArr) > 1) {
            throw new Exception ();
        }
        if (count($hashArr) === 1) {
            $hash = $hashArr[0]['customer_password'];
            return password_verify($pass, $hash);
        }
        return false;
    }
    function getCustomerElementByEmailPass(string $email, string $passValue, string $element) 
    {
        $hashArr = $this->getElements(
            'SELECT customer_password FROM customer WHERE customer_email = ?',
            [$email]
        );
        if (count($hashArr) > 1) {
            throw new Exception ();
        }
        if (count($hashArr) === 1) {
            $hash = $hashArr[0]['customer_password'];
            if (password_verify($passValue, $hash)) {
                return $this->getElements(
                    "SELECT $element FROM customer WHERE customer_email = ?",
                    [$email] 
                )[0][$element];
            }
        }
        return false;
    }
    // function updateCoins(PDO $appDB, $addingCoins, int $valueId)
    // {
    //     if (is_numeric($addingCoins)) {
    //         $this->addElement($appDB, 'buy_coins', [
    //             'customer_id' => $valueId,
    //             'buy_coins_date' => time(),
    //             'buy_coins_sum' => $addingCoins
    //         ]);
    //         return  $this->updateCustomerElementById($appDB, 'customer_coins', $addingCoins + $this->getCustomerElementById($appDB, 'customer_coins', $valueId), $valueId);
    //     }
    //     print (new PageCustomerFacadeNotfound('Notfound'))->getHTML();
    //         exit;
    // }
    function updateCoins($addingCoins, int $customer_id, $whatIsTransaction)
    {
        if (is_numeric($addingCoins)) {
            $date = time();
            $coin_transaction_id = $this->addElements(
                "INSERT INTO coin_transaction (customer_id, coin_transaction_date, coin_transaction_sum, coin_transaction_status) VALUES (?, $date, ?, ?)",
                [$customer_id, $addingCoins, $whatIsTransaction]
            );
            $resentCoinsList = $this->getElements(
                "SELECT customer_coins FROM customer WHERE customer_id = ?",
                [$customer_id]
            );
            if (count($resentCoinsList) > 1) {
                throw new Exception();
            }
            $coins = $resentCoinsList[0]['customer_coins'] + $addingCoins;

            $this->updateElements(
                "UPDATE customer SET customer_coins = $coins WHERE customer_id = ?", 
                [$customer_id]);
            return $coin_transaction_id;
        }
        print (new PageCustomerFacadeNotfound('Notfound'))->getHTML();
            exit;
    }
    // function updateElementsByOneParameter($appDB,  $tableName, $elementName, $elementValue, $parameterName, $parameterValue )
    // {
    //     $appDB->beginTransaction();
    //     $require = $appDB->exec("UPDATE $tableName SET $elementName = '$elementValue' WHERE $parameterName = '$parameterValue'");
    //     if ($require === false) {
    //         $appDB->commit();
    //         return false;
    //     }
    //     $appDB->commit();
    //     return $require;
    // }
    
    function getOneElementArr($query, $parameters, $fieldName) { 
        try {
            $appDB = $this->appDB();
            $appDB->beginTransaction();
            $require = $appDB->prepare($query);
            $exec = $require->execute($parameters);
            if ($exec === false) {
                $appDB->commit();
                return false;
            }
            $elementArr = [];
            while($string = $require->fetch(PDO::FETCH_ASSOC)) {
                $elementArr[] = $string[$fieldName];
            }
            $appDB->commit();
            return $elementArr; // вернет одномерный массив из значений одного столбца
        } catch (PDOException $e) {
            echo $e->getMessage();
            $appDB->commit();
        }
    }

    function updatePass($password, $customer_id)
    {
        $hashPass = $this->hashPass($this->cleanForm($password));
        return $this->updateElements(
            "UPDATE customer SET customer_password = ? WHERE customer_id = ?",
            [$hashPass, $customer_id]
        );
    }

    function getCustomerElement($customer_id, $elementName)
    {
        return $this->getElements(
            "SELECT customer_$elementName FROM customer WHERE customer_id = ?",
            [$customer_id]
        )[0]["customer_$elementName"];
    }

    function getCustomerEmail($customer_id)
    {
        return $this->getCustomerElement($customer_id, 'email');
    }

    function getCustomerCoins($customer_id)
    {
        return $this->getCustomerElement($customer_id, 'coins');
    }

    function getCustomerPasswordRecoveryId($requestUri)
    {
        $urlList = $this->getElements(
            "SELECT customer_password_recovery_id FROM customer_password_recovery WHERE customer_password_recovery_notwork < 1 AND customer_password_recovery_url = ? ORDER BY customer_password_recovery_id DESC",
            [$requestUri]
        );
        return (array_key_exists(0, $urlList)) ? $urlList[0]['customer_password_recovery_id'] : null;
    }

    function getRegistrationEmailId($requestUri)
    {
        $urlList = $this->getElements(
            "SELECT email_for_registration_id FROM email_for_registration WHERE email_for_registration_url = ? AND email_for_registration_notwork < 1 ORDER BY email_for_registration_id DESC",
            [$requestUri]
        );
        return (array_key_exists(0, $urlList)) ? $urlList[0]['email_for_registration_id'] : null;
    }

    function getConditionIdIds()
    {
        return $this->getElements(
            'SELECT condition_id_id FROM condition_id WHERE condition_id_works = 1',
            []
        );
    }

    function getServiceTyptId($serviceTypeName)
    {
        return $this->getElements(
            "SELECT service_type_id FROM service_type WHERE service_type_name = ?",
            [$serviceTypeName]
        )[0]['service_type_id'];
    }
    
    function getDataValues($dataName, $idServiceType, $conditionId)
    {
        return $this->getElements(
            "SELECT data_value FROM condition_value WHERE data_name = ? AND service_type_id = ? AND condition_id_id = ?",
            [$dataName, $idServiceType, $conditionId]
        );
    }
    
}