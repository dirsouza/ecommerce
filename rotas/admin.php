<?php

use Hcode\PageAdmin;
use Hcode\Model;
use Hcode\Model\User;

$app->get('/admin', function() {

    User::verifyLogin();

    $user = new User();

    $user->get($_SESSION[User::SESSION]['iduser']);

    $page = new PageAdmin(array(
        'data'=>array(
            'user'=>$user->getValues()
        )
    ));

    $page->setTpl('index');
});

require_once 'admin-login.php';
require_once 'admin-users.php';
require_once 'admin-forgot.php';
require_once 'admin-categories.php';
require_once 'admin-products.php';