<?php

use Hcode\PageAdmin;
use Hcode\Model;
use Hcode\Model\User;
use Hcode\Model\Product;

$app->get('/admin/products', function() {
    User::verifyLogin();
    
    $user = new User();
    $user->get($_SESSION[User::SESSION]['iduser']);
    
    $products = Product::listAll();

    $page = new PageAdmin(array(
        'data'=>array(
            'user'=>$user->getValues()
        )
    ));
    
    $page->setTpl('products', array(
        'products' => $products
    ));
});

$app->get('/admin/products/create', function() {
    User::verifyLogin();
    
    $user = new User();
    $user->get($_SESSION[User::SESSION]['iduser']);
    
    $products = Product::listAll();

    $page = new PageAdmin(array(
        'data'=>array(
            'user'=>$user->getValues()
        )
    ));
    
    $page->setTpl('products-create');
});

$app->post('/admin/products/create', function() {
    User::verifyLogin();
    
    $product = new Product();
    $product->setData($_POST);
    $product->save();
    
    header("location: /admin/products");
    
    exit;
});

$app->get('/admin/products/:idproduct/delete', function($idproduct) {
    User::verifyLogin();
    
    $product = new Product();
    $product->get((int)$idproduct);
    $product->delete();
    
    header("location: /admin/products");
    
    exit;
});

$app->get('/admin/products/:idproduct', function($idproduct) {
    User::verifyLogin();
    
    $user = new User();
    $user->get($_SESSION[User::SESSION]['iduser']);

    $product = new Product();
    $product->get((int) $idproduct);

    $page = new PageAdmin(array(
        'data'=>array(
            'user'=>$user->getValues()
        )
    ));

    $page->setTpl("products-update", array(
        'product' => $product->getValues()
    ));
});

$app->post('/admin/products/:idproduct', function($idproduct) {
    User::verifyLogin();

    $product = new Product();
    $product->get((int) $idproduct);
    $product->setData($_POST);
    $product->update();
    $product->setPhoto($_FILES['file']);
    
    header("location: /admin/products");
    
    exit;
});