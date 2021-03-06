<?php

use Hcode\Page;
use Hcode\Model;
use Hcode\Model\Category;

$app->get('/categories/:idcategory', function($idcategory) {
    $category = new Category();
    $category->get((int)$idcategory);
    
    $page = new Page();
    $page->setTpl('category', array(
        'category' => $category->getValues(),
        'products' => []
    ));
});