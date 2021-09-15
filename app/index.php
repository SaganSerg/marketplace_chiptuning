<?php
include 'var.php';
include 'functions.php';
spl_autoload_register($for_spl_autoload_register);
($cookiesmanagement = CookiesManagement::getInstance())->setAgree();
Controller::printPage();