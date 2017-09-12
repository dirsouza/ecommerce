<?php

use Hcode\PageAdmin;
use Hcode\Model;
use Hcode\Model\User;

$app->get('/admin/users', function() {

    User::verifyLogin();

    $users = User::listAll();

    $user = new User();

    $user->get($_SESSION[User::SESSION]['iduser']);

    $page = new PageAdmin(array(
        'data'=>array(
            'user'=>$user->getValues()
        )
    ));

    $page->setTpl('users', array(
        'users' => $users
    ));
});

$app->get('/admin/users/create', function() {

    User::verifyLogin();

    $user = new User();

    $user->get($_SESSION[User::SESSION]['iduser']);

    $page = new PageAdmin(array(
        'data'=>array(
            'user'=>$user->getValues()
        )
    ));

    $page->setTpl("users-create");
});

$app->get('/admin/users/:iduser/delete', function($iduser) {

    User::verifyLogin();

    $user = new User();

    $user->get((int) $iduser);

    $user->delete();

    header("location: /admin/users");

    exit;
});

$app->get('/admin/users/:iduser', function($iduser) {

    User::verifyLogin();

    $user = new User();

    $user->get((int) $iduser);

    $page = new PageAdmin(array(
        'data'=>array(
            'user'=>$user->getValues()
        )
    ));

    $page->setTpl("users-update");
});

$app->post('/admin/users/create', function() {

    User::verifyLogin();

    $user = new User();

    $_POST['inadmin'] = (isset($_POST['inadmin'])) ? 1 : 0;

    $user->setData($_POST);

    $user->save();

    header("location: /admin/users");

    exit;
});

$app->post('/admin/users/:iduser', function($iduser) {

    User::verifyLogin();

    $user = new User();

    $_POST['inadmin'] = (isset($_POST['inadmin'])) ? 1 : 0;

    $user->get((int) $iduser);

    $user->setData($_POST);

    $user->update();

    header("location: /admin/users");

    exit;
});