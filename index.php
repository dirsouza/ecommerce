<?php

session_start();

require_once("vendor/autoload.php");

use Slim\Slim;
use Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {

    $page = new Page();

    $page->setTpl("index");
});

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

$app->get('/admin/forgot', function() {

    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);

    $page->setTpl("forgot");
});

$app->post('/admin/forgot', function() {

    $user = User::getForgot($_POST['email']);

    header("location: /admin/forgot/sent");

    exit;
});

$app->get('/admin/forgot/sent', function() {

    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);

    $page->setTpl("forgot-sent");
});

$app->get('/admin/forgot/reset', function() {

    $user = User::validForgotDecrypt($_GET['code']);

    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);

    $page->setTpl("forgot-reset", array(
        'name' => $user['desperson'],
        'code' => $_GET['code']
    ));
});

$app->post('/admin/forgot/reset', function() {

    $forgot = User::validForgotDecrypt($_POST['code']);

    User::setForgotUser($forgot['idrecovery']);

    $user = new User();

    $user->get((int) $forgot['iduser']);

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT, array(
        'cost' => 12
    ));

    $user->setPassword($password);

    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);

    $page->setTpl("forgot-reset-success");
});

$app->get('/admin/categories', function() {
    User::verifyLogin();
    
    $user = new User();
    $user->get($_SESSION[User::SESSION]['iduser']);
    
    $categories = Category::listAll();

    $page = new PageAdmin(array(
        'data'=>array(
            'user'=>$user->getValues()
        )
    ));

    $page->setTpl('categories', array(
        'categories' => $categories
    ));
});

$app->get('/admin/categories/create', function() {

    User::verifyLogin();

    $user = new User();
    $user->get($_SESSION[User::SESSION]['iduser']);

    $page = new PageAdmin(array(
        'data'=>array(
            'user'=>$user->getValues()
        )
    ));

    $page->setTpl("categories-create");
});

$app->post('/admin/categories/create', function() {

    User::verifyLogin();
    
    $category = new Category();

    $category->setData($_POST);

    $category->save();

    header("location: /admin/categories");

    exit;
});

$app->get('/admin/categories/:idcategory/delete', function($idcategory) {

    User::verifyLogin();

    $category = new Category();

    $category->get((int) $idcategory);

    $category->delete();

    header("location: /admin/categories");

    exit;
});

$app->get('/admin/categories/:idcategory', function($idcategory) {

    User::verifyLogin();
    
    $user = new User();
    $user->get($_SESSION[User::SESSION]['iduser']);

    $category = new Category();
    $category->get((int) $idcategory);

    $page = new PageAdmin(array(
        'data'=>array(
            'user'=>$user->getValues()
        )
    ));

    $page->setTpl("categories-update", array(
        'category' => $category->getValues()
    ));
});

$app->post('/admin/categories/:idcategory', function($idcategory) {
    User::verifyLogin();

    $category = new Category();

    $category->get((int) $idcategory);

    $category->setData($_POST);

    $category->update();

    header("location: /admin/categories");

    exit;
});

$app->get('/categories/:idcategory', function($idcategory) {
    $category = new Category();
    $category->get((int)$idcategory);
    
    $page = new Page();
    $page->setTpl('category', array(
        'category' => $category->getValues(),
        'products' => []
    ));
});

$app->run();
