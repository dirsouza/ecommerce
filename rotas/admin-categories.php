<?php

use Hcode\PageAdmin;
use Hcode\Model;
use Hcode\Model\User;
use Hcode\Model\Category;

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