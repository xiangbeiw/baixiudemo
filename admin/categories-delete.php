<?php
require_once '../functions.php';

if(empty($_GET['id'])){
	exit('缺少必要参数');
}

$id = $_GET['id'];
//sql注入 id = 1 or 1 = 1 delete all 

//删除单个
//$rows = xiu_execute('delete from categories where id = '.$id);

$rows = xiu_execute('delete from categories where id in ('.$id.');');
header('location: /admin/categories.php');
