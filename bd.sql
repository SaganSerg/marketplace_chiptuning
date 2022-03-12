-- создание базы данных
CREATE DATABASE chiptuning3 CHARACTER SET utf8mb4;

-- на удаленном сервере 
ALTER DATABASE wuloruwu_chiptuning3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- код для создания пользователя проверенный
-- пароль RjyBcgLfy
CREATE USER 'admin_chiptuning3'@'localhost' IDENTIFIED BY '123456'; 
GRANT SELECT, INSERT, UPDATE  ON chiptuning3.* TO 'admin_chiptuning3'@'localhost';
-- код для создание пользователя. Надо проверить как это работает
GRANT SELECT, INSERT, UPDATE  ON chiptung3.* TO 'admin_chiptuning3'@'localhost' IDENTIFIED BY '123456';

-- для того, чтобы можно было исползовать базу данных надо выбрать ее
USE chiptuning3;

-- на удаленном сервере
USE wuloruwu_chiptuning3;

-- создаем таблицы
CREATE TABLE email_for_registration /* в данной таблице хранится информация об электронных адресах, которые ввел потенциальный клиент, до того как было подтвеждено что данная почта действительно его */
(
	email_for_registration_id INT UNSIGNED NOT NULL PRIMARY KEY,
	email_for_registration_email CHAR(50) NOT NULL,
	email_for_registration_url CHAR(255) DEFAULT 0, /* предварительно, данный урл будет формироваться на основе значения customer_email_id плюс слово registration */
	email_for_registration_date_of_link_creation BIGINT NOT NULL,
    email_for_registration_notwork SMALLINT DEFAULT 0
)
ENGINE=INNODB;

CREATE TABLE customer
(
    customer_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_email CHAR(50) NOT NULL UNIQUE,
    customer_telephone CHAR(11) NOT NULL,
    customer_valuta CHAR(3) NOT NULL,
    customer_language CHAR(2) NOT NULL,
    customer_registration_date BIGINT NOT NULL,
    customer_coins SMALLINT DEFAULT 0,
    customer_password CHAR(255) NOT NULL
)
ENGINE=INNODB;

CREATE TABLE provider
(
    provider_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    provider_login CHAR(50) NOT NULL UNIQUE,
    provider_firstname CHAR(50) NOT NULL,
    provider_secondname CHAR(50) NOT NULL,
    provider_registration_date BIGINT NOT NULL,
    provider_status CHAR(15) NOT NULL,
    provider_password CHAR(255) NOT NULL
)
ENGINE=INNODB;

/* 
INSERT INTO provider (provider_login, provider_firstname, provider_secondname, provider_registration_date, provider_status, provider_password) VALUES ('Vasa', 'Vasiliy', 'Ivanov', 1639056038, 'file_treatment', '$2y$10$9heuJzptkGBWibXoIjiw9.rCHmeo1Mb.ZjA194pr9Wt/HEl3Zvd2e' )
*/
CREATE TABLE valuta_exchange_rate
(
    valuta_exchange_rate_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    valuta_exchange_rate_data BIGINT NOT NULL,
    valuta_name CHAR(3) NOT NULL,
    valuta_exchange_rate_value FLOAT(5,2) NOT NULL
)
ENGINE=INNODB;

CREATE TABLE coin_transaction
(
    coin_transaction_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    coin_transaction_date BIGINT NOT NULL,
    coin_transaction_sum SMALLINT NOT NULL, -- сумма в коинах
    coin_transaction_status CHAR(25) NOT NULL, -- в данном поле указывается какая это транзакция зачисление денег toPaySystem или списание коинов downCoins

    FOREIGN KEY (customer_id) REFERENCES customer(customer_id)
)
ENGINE=INNODB;

CREATE TABLE pay_system_transaction
(
    pay_system_transaction_id  INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    coin_transaction_id INT UNSIGNED NOT NULL,
    valuta_exchange_rate_id INT UNSIGNED NOT NULL,
    pay_system_transaction_order_id CHAR(255) DEFAULT 0,-- сюда записывается id сделки, который присваивает сбер-банк
    pay_system_transaction_notactivelink CHAR(1) DEFAULT 0, -- здесь стоит метка был ли переход по данной ссылке


    FOREIGN KEY (valuta_exchange_rate_id) REFERENCES valuta_exchange_rate(valuta_exchange_rate_id),
    FOREIGN KEY (coin_transaction_id) REFERENCES coin_transaction(coin_transaction_id)
)

CREATE TABLE customer_password_recovery
(
    customer_password_recovery_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_password_recovery_url CHAR(255) NOT NULL,
    customer_password_recovery_notwork SMALLINT DEFAULT 0,
    customer_password_recovery_date_of_link_creation BIGINT NOT NULL,
    customer_password_recovery_date_of_visit_link BIGINT DEFAULT 0,
    customer_password_recovery_date_password BIGINT ,
    customer_id INT UNSIGNED NOT NULL,

    FOREIGN KEY (customer_id) REFERENCES customer(customer_id) 
)
ENGINE=INNODB;

CREATE TABLE service_type
(
    service_type_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    service_type_name CHAR(50) NOT NULL UNIQUE
)
ENGINE=INNODB;

CREATE TABLE customer_order
( 
    customer_order_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    service_type_id INT UNSIGNED NOT NULL,
    customer_order_amount SMALLINT NOT NULL,
    customer_order_date BIGINT NOT NULL,
    customer_order_status CHAR(15) NOT NULL,
    provider_id INT UNSIGNED,
    customer_order_pay_date BIGINT,

    FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
    FOREIGN KEY (service_type_id) REFERENCES service_type(service_type_id),
    FOREIGN KEY (provider_id) REFERENCES provider(provider_id)
)
ENGINE=INNODB;

CREATE TABLE change_order_status_provider
(
    change_order_status_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    change_order_status_date BIGINT NOT NULL,
    change_order_status_status CHAR(15) NOT NULL,
    provider_id INT UNSIGNED NOT NULL,
    customer_order_id INT UNSIGNED NOT NULL,

    FOREIGN KEY (customer_order_id) REFERENCES customer_order(customer_order_id),
    FOREIGN KEY (provider_id) REFERENCES provider(provider_id)
)
ENGINE=INNODB;

CREATE TABLE deal_payment
(
    deal_payment_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    coin_transaction_id INT UNSIGNED NOT NULL,
    customer_order_id INT UNSIGNED NOT NULL,

    FOREIGN KEY (customer_order_id) REFERENCES customer_order(customer_order_id),
    FOREIGN KEY (coin_transaction_id) REFERENCES coin_transaction(coin_transaction_id)
)
ENGINE=INNODB;

CREATE TABLE change_provider 
(
    change_provider_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_order_id INT UNSIGNED NOT NULL,
    provider_id INT UNSIGNED NOT NULL,
    change_provider_date BIGINT NOT NULL,
    customer_order_status CHAR(15),

    FOREIGN KEY (customer_order_id) REFERENCES customer_order(customer_order_id),
    FOREIGN KEY (provider_id) REFERENCES provider(provider_id)
)
ENGINE=INNODB;

CREATE TABLE  message
( 
    message_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_order_id INT UNSIGNED NOT NULL,
    message_content TEXT NOT NULL, 
    message_from ENUM('customer','provider') NOT NULL,
    message_date BIGINT NOT NULL,
    message_seen CHAR(1) NOT NULL,

    FOREIGN KEY (customer_order_id) REFERENCES customer_order(customer_order_id)
)
ENGINE=INNODB;

CREATE TABLE service 
(
    service_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    service_name CHAR(50) NOT NULL UNIQUE
)
ENGINE=INNODB;

CREATE TABLE condition_id
(
    condition_id_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    condition_id_works CHAR(1) NOT NULL /* значение 1 (единица) говорит что данная кондиция работает */
)
ENGINE=INNODB;

CREATE TABLE condition_value /* в данную базу нужно вносить только новые кондиции, изменять старые нельзя */
(
    condition_value_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    condition_id_id INT UNSIGNED NOT NULL,
    data_name CHAR(50) NOT NULL,
    data_value CHAR(50) NOT NULL,
    service_type_id INT UNSIGNED NOT NULL,

    FOREIGN KEY (condition_id_id) REFERENCES condition_id(condition_id_id),
    FOREIGN KEY (service_type_id) REFERENCES service_type(service_type_id)
)
ENGINE=INNODB;

CREATE TABLE constant_value 
(
    constant_value_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    service_type_id INT UNSIGNED NOT NULL,
    constant_value_name CHAR(50) NOT NULL,
    constant_value_value CHAR(50),

    FOREIGN KEY (service_type_id) REFERENCES service_type(service_type_id)
)
ENGINE=INNODB;

CREATE TABLE condition_service 
(
    condition_service_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    condition_id_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED NOT NULL,
    condition_service_price SMALLINT NOT NULL,

    FOREIGN KEY (condition_id_id) REFERENCES condition_id(condition_id_id),
    FOREIGN KEY (service_id) REFERENCES service(service_id)
)
ENGINE=INNODB;

CREATE TABLE customer_order_data 
(
    customer_order_data_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_order_id INT UNSIGNED NOT NULL,
    customer_order_data_name CHAR(50) NOT NULL,
    customer_order_data_value CHAR(50) NOT NULL,

    FOREIGN KEY (customer_order_id) REFERENCES customer_order(customer_order_id)
)
ENGINE=INNODB;

/*
CREATE TABLE customer_order_service 
(
    customer_order_service_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_order_id INT UNSIGNED NOT NULL,
    condition_service_id INT UNSIGNED NOT NULL,

    FOREIGN KEY (customer_order_id) REFERENCES customer_order(customer_order_id),
    FOREIGN KEY (condition_service_id) REFERENCES condition_service(condition_service_id)
);
*/

CREATE TABLE customer_order_service
(
    customer_order_service_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_order_id INT UNSIGNED NOT NULL,
    service_name CHAR(50) NOT NULL,

    FOREIGN KEY (customer_order_id) REFERENCES customer_order(customer_order_id)
)
ENGINE=INNODB;

CREATE TABLE file_path
(
    file_path_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_order_id INT UNSIGNED NOT NULL,
    file_path_date BIGINT NOT NULL,
    file_path_path CHAR(255) NOT NULL,
    file_path_what_file CHAR(50) NOT NULL, /* имеется ввиду здесь храниться информация о том, что это за файл. Переданный на обработку или наоборот уже обработанный */
    file_path_checksum CHAR(255) NOT NULL,
    
    FOREIGN KEY (customer_order_id) REFERENCES customer_order(customer_order_id)
)
ENGINE=INNODB;
--
DROP database chiptuning3;

-- 
INSERT INTO service_type (service_type_name) VALUES ('file treatment');
INSERT INTO service_type (service_type_name) VALUES ('consultation');
--
--
-- INSERT INTO service (service_name) VALUES ('egr off');
-- INSERT INTO service (service_name) VALUES ('cat off');
-- INSERT INTO service (service_name) VALUES ('guc artrima mod');
-- INSERT INTO service (service_name) VALUES ('checksum correction');
-- INSERT INTO service (service_name) VALUES ('dpf off');
-- INSERT INTO service (service_name) VALUES ('speed limit');
-- INSERT INTO service (service_name) VALUES ('dtc off');
-- INSERT INTO service (service_name) VALUES ('ori file request');
INSERT INTO service (service_name) VALUES ('SCR-off');
INSERT INTO service (service_name) VALUES ('Power');
INSERT INTO service (service_name) VALUES ('DTC-off');

INSERT INTO service (service_name) VALUES ('EGR-off');
INSERT INTO service (service_name) VALUES ('CAT-off');
INSERT INTO service (service_name) VALUES ('guc artrima mod');
INSERT INTO service (service_name) VALUES ('checksum correction');
INSERT INTO service (service_name) VALUES ('DPF-off');
--
--
INSERT INTO condition_id (condition_id_works) VALUES(1);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle type',
    'truck',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle brand',
    'KAMAZ',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle model',
    '740',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'ecu',
    'EDC7UC31',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'SCR-off'),
    200
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'DTC-off'),
    100
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'Power'),
    400
);




INSERT INTO condition_id (condition_id_works) VALUES(1);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle type',
    'car',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle brand',
    'bmw',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle model',
    'x6',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'ecu',
    '2ggg',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'EGR-off'),
    200
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'CAT-off'),
    100
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'guc artrima mod'),
    400
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'checksum correction'),
    20
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'DPF-off'),
    300
);




INSERT INTO condition_id (condition_id_works) VALUES(1);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle type',
    'morine',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle brand',
    'morina1',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle model',
    'somethingMorine',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'ecu',
    '2ggg',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'guc artrima mod'),
    400
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'checksum correction'),
    20
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'DPF-off'),
    300
);



INSERT INTO condition_id (condition_id_works) VALUES(1);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle type',
    'morine',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle brand',
    'morina2',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle model',
    'somethingMorine2',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'ecu',
    '2ggg444',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'EGR-off'),
    200
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'CAT-off'),
    100
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'checksum correction'),
    20
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'DPF-off'),
    300
);




INSERT INTO condition_id (condition_id_works) VALUES(1);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle type',
    'morine',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle brand',
    'morina3',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle model',
    'somethingMorine3',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'ecu',
    '2ggg444',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'EGR-off'),
    200
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'CAT-off'),
    100
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'checksum correction'),
    20
);



INSERT INTO condition_id (condition_id_works) VALUES(1);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle type',
    'morine',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle brand',
    'morina4',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'vehicle model',
    'somethingMorine4',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_value (condition_id_id, data_name, data_value, service_type_id) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    'ecu',
    '2ggg444',
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1)
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'EGR-off'),
    200
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'CAT-off'),
    100
);
INSERT INTO condition_service (condition_id_id, service_id, condition_service_price) VALUES (
    (SELECT condition_id_id FROM condition_id ORDER BY condition_id_id DESC LIMIT 1),
    (SELECT service_id FROM service WHERE service_name = 'checksum correction'),
    20
);
--

--
--
INSERT INTO constant_value (service_type_id, constant_value_name) VALUES (
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1),
    'plate of vehicle'
);
INSERT INTO constant_value (service_type_id, constant_value_name) VALUES (
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1),
    'vin'
);
INSERT INTO constant_value (service_type_id, constant_value_name, constant_value_value) VALUES (
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1),
    'reading device',
    'Dimsport NewGenius'
);
INSERT INTO constant_value (service_type_id, constant_value_name, constant_value_value) VALUES (
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1),
    'reading device',
    'Dimsport Trasdata'
);
INSERT INTO constant_value (service_type_id, constant_value_name, constant_value_value) VALUES (
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1),
    'reading device',
    'Alientech K Tag'
);
INSERT INTO constant_value (service_type_id, constant_value_name, constant_value_value) VALUES (
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1),
    'reading device',
    'Alientech Kess V2'
);
INSERT INTO constant_value (service_type_id, constant_value_name, constant_value_value) VALUES (
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1),
    'reading device',
    'Alientech Powergate'
);
INSERT INTO constant_value (service_type_id, constant_value_name, constant_value_value) VALUES (
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1),
    'reading device',
    'AutoTuner'
);
INSERT INTO constant_value (service_type_id, constant_value_name, constant_value_value) VALUES (
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1),
    'reading device',
    'Byteshooter OBD'
);
INSERT INTO constant_value (service_type_id, constant_value_name, constant_value_value) VALUES (
    (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1),
    'reading device',
    'Byteshooter Toolbox'
);
--
--

--
--
INSERT INTO customer (customer_email, customer_telephone, customer_valuta, customer_language, customer_registration_date, customer_password) 
VALUES ('mail@mail.ru', '42123121133', 'rub', 'ru', 231321546546, 'lkjlkj54654kljklkj');
--

--
INSERT INTO customer_order (customer_id, service_type_id, customer_order_amount, customer_order_date, customer_order_status) 
VALUES ('1', '1', 800, 321313213213, 'notpaid');
--

--
INSERT INTO customer_order_data (customer_order_id, customer_order_data_name, customer_order_data_value) 
VALUES (1, 'vehicle type', 'car'); 
INSERT INTO customer_order_data (customer_order_id, customer_order_data_name, customer_order_data_value) 
VALUES (1, 'vehicle brand', 'bmw'); 
INSERT INTO customer_order_data (customer_order_id, customer_order_data_name, customer_order_data_value) 
VALUES (1, 'vehicle model', 'x6'); 
INSERT INTO customer_order_data (customer_order_id, customer_order_data_name, customer_order_data_value) 
VALUES (1, 'ecu', '2'); 
INSERT INTO customer_order_data (customer_order_id, customer_order_data_name, customer_order_data_value) 
VALUES (1, 'plate of vehicle', 'something1'); 
INSERT INTO customer_order_data (customer_order_id, customer_order_data_name, customer_order_data_value) 
VALUES (1, 'vin', 'other1');
INSERT INTO customer_order_data (customer_order_id, customer_order_data_name, customer_order_data_value) 
VALUES (1, 'reading device', 'Alientech K Tag');
--

--
/*
Когда клиет переходит на страничку где отображаются разные виды типов услуг, нам нужно знать какие услуги мы оказываем и мы их выдергиваем из базы */
SELECT service_type_name, service_type_id FROM service_type;
/* после этого мы передаем клиенту список имен услуг и их id-шники */
/* Далее клиент щелкает по имени услуги и нам передает id-шник данного типа услуг */
/* Мы исходя и порядка работы приложения, знаем какую data-name мы должны запросить первой, чтобы отобразить значения в поле выбора. В нашем случае это 'vehicle type' */
/* получается что мы знаем id-шник вида услуг полученный от клиента и data-name который мы знаем исходя из порядка работы приложения */
/* Получаем из базы перечень data-value соответсвующих data-name и список condition_id_id */
SELECT v.data_value, v.condition_id_id FROM condition_id i INNER JOIN condition_value v ON i.condition_id_id = v.condition_id_id WHERE v.service_type_id = 1 AND v.data_name = 'vehicle type';
/* Получив из базы data-value и condition_id_id мы видим, что одно и то же значение data-value имеет разные condition_id_id. И нам нужно сделать соответсвие значению data-value значениям condition_id_id, записав из ввиде строки через запятую, чтобы можно было эти значения присвоить выпадающему списку, вид значения может быть такой "vehicle type:car:1,3,5" */
/* когда клиент щелкаем по выпадающему списку мы получаем от него строку вида "car:1,2" */
SELECT data_value, condition_id_id FROM condition_value WHERE data_name = 'vehicle brand' AND condition_id_id IN (1, 2); 
/* получив из базы data_value и condition_id_id, получаем соответвие data_value и condition_id_id и также значение condition_id_id собираем в строку и присваиваем это значение элементам выпадающего списка и так же помещаем в форму скрытое поле в качестве значения name 'car' т.е. значение которое мы должны извлечь из строки*/
/* когда клиент кликает по выпадающему списку мы получаем значение '1,2' и мы знаем какой data_name должен быть */
SELECT data_value, condition_id_id FROM condition_value WHERE data_name = 'vehicle model' AND condition_id_id IN (1, 2); 
/* тоже самое */
SELECT data_value, condition_id_id FROM condition_value WHERE data_name = 'ecu' AND condition_id_id IN (1, 2); 
/* то же самое*/
/* при клике по выпадающему списку нам будет отправлен все скрытые поля + строка 'ecu:2ggg:2'*/


SELECT s.service_name, c.condition_service_price, c.condition_service_id FROM condition_service c INNER JOIN service s ON s.service_id = c.service_id WHERE c.condition_id_id = 2;
/* это мы получили список услуг для отображения в чек боксах */
/* а потом */ 
INSERT INTO customer_order_service (customer_order_id, condition_service_id) VALUES (1, 1);
--

--
/* Когда клиент кликает по ссылке на конкретную услугу, мы получаем от клиента service_type_id типа услуги и по этому id можем получить список типов транспортного средства */

SELECT v.data_value, v.condition_id_id FROM condition_id i INNER JOIN condition_value v ON i.condition_id_id = v.condition_id_id WHERE v.service_type_id = 1 AND v.data_name = 'vehicle type';

/* Когда клиент выбирает тип транспортного средства он отправляет нам его название и */

/* 

*/
SELECT DISTINCT v.condition_id_id FROM condition_id i INNER JOIN condition_value v ON i.condition_id_id = v.condition_id_id WHERE v.service_type_id = (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1);

SELECT DISTINCT v.data_value FROM condition_id i INNER JOIN condition_value v ON i.condition_id_id = v.condition_id_id WHERE v.service_type_id = (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1) AND v.data_name = 'vehicle type';

SELECT DISTINCT v.data_value FROM condition_value v WHERE v.condition_id_id IN ((SELECT DISTINCT condition_id_id FROM condition_value WHERE data_name = 'vehicle type' AND data_value = 'car')) AND v.data_name = 'vehicle brand';

SELECT DISTINCT v.data_value FROM condition_value v WHERE v.condition_id_id IN (
    (SELECT DISTINCT condition_id_id FROM condition_value WHERE data_name = 'vehicle brand' AND data_value = 'bmw' AND condition_id_id IN (
        (SELECT DISTINCT v.condition_id_id FROM condition_value v WHERE v.condition_id_id IN (
                (SELECT DISTINCT condition_id_id FROM condition_value WHERE data_name = 'vehicle type' AND data_value = 'car')
            ) AND v.data_name = 'vehicle brand')
        )
    )
) AND v.data_name = 'vehicle model';



SELECT DISTINCT v.condition_id_id FROM condition_id i INNER JOIN condition_value v ON i.condition_id_id = v.condition_id_id WHERE v.service_type_id = (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1) AND v.data_name = 'vehicle type';

SELECT v.condition_id_id FROM condition_value v WHERE v.condition_id_id IN (6, 7) AND v.data_name = 'vehicle brand';


SELECT v.data_value, v.condition_id_id FROM condition_value v WHERE v.condition_id_id IN (6, 7) AND v.data_name = 'vehicle brand';

SELECT v.data_value, v.condition_id_id FROM condition_value v WHERE v.condition_id_id IN (6, 7) AND v.data_name = 'vehicle model';


SELECT DISTINCT v.data_value FROM condition_id i INNER JOIN condition_value v ON i.condition_id_id = v.condition_id_id WHERE v.service_type_id = (SELECT service_type_id FROM service_type WHERE service_type_name = 'file treatment' ORDER BY service_type_id DESC LIMIT 1) AND v.data_name = 'vehicle type';


--
--
INSERT INTO customer_order_service (customer_order_id, condition_service_id)
VALUES (1, (SELECT condition_service_id FROM condition_service WHERE condition_id_id = (SELECT )));
/* 
надо получить condition_service_id, для того, чтобы получить его нужно знать service_id (можно получить по параметру полученному от клиента) и condition_id_id ()
*/
SELECT condition_value_id FROM condition_value WHERE data_name = 'vehicle type' AND data_value = 'car';
SELECT condition_value_id FROM condition_value WHERE data_name = 'vehicle brand' AND data_value = 'bmw';
SELECT condition_value_id FROM condition_value WHERE data_name = 'vehicle model' AND data_value = 'x6';

-- регисрация нового сотрудника
INSERT INTO provider (provider_login, provider_firstname, provider_secondname, provider_registration_date, provider_status, provider_password) VALUES ('Vasa', 'Vasiliy', 'Ivanov', 1639056038, 'file_treatment', '$2y$10$mabqiowX1cpNsPYy5B60KOxM9xizdF7wMkd3wCm3qT2/7b3GNCUiK');
INSERT INTO provider (provider_login, provider_firstname, provider_secondname, provider_registration_date, provider_status, provider_password) VALUES ('Peta', 'Peter', 'Ivanov', 1639056038, 'file_treatment', '$2y$10$mabqiowX1cpNsPYy5B60KOxM9xizdF7wMkd3wCm3qT2/7b3GNCUiK');
--
--
