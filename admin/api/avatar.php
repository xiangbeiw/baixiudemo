<?php
require_once '../../config.php';
if(empty($_GET['email'])){
	exit('缺少必要參數!');
}
$email = $_GET['email'];

$conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);

if(!$conn){
	exit('连接数据库失败');
}

$query = mysqli_query($conn,"select avatar from users where email = '{$email}' limit 1;");
if(!$query){
	exit('查询失败');
}

$row = mysqli_fetch_assoc($query);

echo $row['avatar'];