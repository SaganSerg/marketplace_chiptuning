<?php
$dbhost = "localhost";
$dbname = 'chiptuning3';
// $dbname = 'wuloruwu_chiptuning3'; // боевая
$dbadmin = 'admin_chiptuning3';
// $dbadmin = 'wuloruwu_admin_chiptuning3'; // боевая
$dbadminpass = '123456';
// $dbadminpass = 'RjyBcgLfy'; // боевая
$rub = 'RUB';
$usd = 'USD';
$eur = 'EUR';
$domain = 'marketplace_chiptuning.loc';
// $domain = 'chiptuning.wulo.ru'; // боевая
$protocol = 'http';
$saveFilePath = '/opt/lampp/'; 
// $saveFilePath = '/home/wuloruwu/'; // боевая
$incomingFileDir = 'chiptuning3_incoming';
$outgoingFileDir = 'chiptuning3_outgoing';
// название типа файла, которое помещается в таблица file_path в поле filt_path_what_file
// файл котрый уже обработан нашей компание т.е. файл протинюнгованный
$treatmentedFile = 'treatmented';
// файл, который только поступил от заказчика, еще НЕ протюнингованный
$notTreatmentedFile = 'nottreatmented';

$fileSizeFromCustomer = 10*1024*1024;
$ourMail = 'websagan@gmail.com';
// $ourMail = 'wuloruwu@vh-cpanel4.area.netfox.ru';
$saveLinkPasswordTime = 60*60*24;
$accessRight = 0777;

// статусы сделок
// оплаченная сделка
$paidDealStatus = 'paid';
// сделка в работе
$beingDoneDealStatus = 'being_done';
// сделка не оплачена
$unpaidDealStatus = 'unpaid';
// сделка сделана
$doneDealStatus = 'done';


// статусы сотрудников
// file_treatmenter может отвечать в чате на вопросы, (может Скачивать файлы от клиента, может ЗАкачивать файлы обработанные, может менять статусы)
$fileTreatmenterProviderStatus = 'file_treatment';

// данные сбербанка
$sberPass = 123456;
$sberUserName = 'somebody';

// $example = {
//     "errorCode":"0",
//     "errorMessage":"Успешно",
//     "orderNumber":"0784sse49d0s134567890",
//     "orderStatus":6,
//     "actionCode":-2007,
//     "actionCodeDescription":"Время сессии истекло",
//     "amount":33000,
//     "currency":"643",
//     "date":1383819429914,
//     "orderDescription":" ",
//     "merchantOrderParams":[
//         {
//             "name":"email",
//             "value":"yap"
//         }
//     ],
//     "attributes":[
//         {
//             "name":"mdOrder",
//             "value":"b9054496-c65a-4975-9418-1051d101f1b9"
//         }
//     ],
//     "cardAuthInfo":{
//         "expiration":"201912",
//         "cardholderName":"Ivan",
//         "secureAuthInfo":
//         {
//             "eci":6,"threeDSInfo":{
//                 "xid":"MDAwMDAwMDEzODM4MTk0MzAzMjM="
//             }
//         },
//         "pan":"411111**1111"
//     },
//     "terminalId":"333333"
// };