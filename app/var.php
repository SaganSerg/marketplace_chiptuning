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
$domain = 'chiptuning3.localhost';
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
// $ourMail = 'websagan@gmail.com';
$ourMail = 'wuloruwu@vh-cpanel4.area.netfox.ru';
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
