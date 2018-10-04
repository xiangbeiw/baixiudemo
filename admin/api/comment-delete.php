<?php
require_once '../../functions.php';

if (empty($_GET['id'])) {
	exit('缺少必要参数');
}

$id = $_GET['id'];
//sql注入 id = 1 or 1 = 1 delete all

//删除单个
//$rows = xiu_execute('delete from categories where id = '.$id);

$rows = xiu_execute('delete from comments where id in (' . $id . ');');
header('Content-Type: application/json');
echo json_encode($rows > 0);
//if($rows > 0){
//echo 123;
//}else{echo 456;}

//header('location: /admin/comments.php');
