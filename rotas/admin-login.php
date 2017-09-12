<?php

use Hcode\PageAdmin;
use Hcode\Model;
use Hcode\Model\User;

$app->get('/admin/login', function() {

    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);

    $page->setTpl('login');
});

$app->get('/admin/login/error', function() {

    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);

    $page->setTpl("login-error");
});

$app->post('/admin/login', function() {

    User::login($_POST['login'], $_POST['password']);

    header("location: /admin");

    exit;
});

$app->get('/admin/logout', function() {

    User::logout();

    header("location: /admin/login");

    exit;
});