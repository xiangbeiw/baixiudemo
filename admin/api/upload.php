<?php

//var_dump($_FILES['avatar']);

//接受文件
//保存文件
//返回保存后的url
if(empty($_FILES['avatar'])){
	exit('必选上传文件');
}

$avatar = $_FILES['avatar'];
if($avatar['error'] !==  UPLOAD_ERR_OK){
	exit('上传失败');
}

//移动文件到网站范围之内
$ext = pathinfo($avatar['name'],PATHINFO_EXTENSION);
$target = '../../static/uploads/img-'.uniqid().'.'.$ext;

if(!move_uploaded_file($avatar['tmp_name'], $target)){
	exit('移动失败');
}

echo substr($target,5);