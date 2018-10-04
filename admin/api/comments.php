<?php
require_once '../../functions.php';
//接收客户端的ajax请求，返回评论数据
//$comments = xiu_fetch_all("	select comments.*,
//	posts.title as post_title
//	from comments
//	INNER JOIN posts on comments.post_id = posts.id
//	order by comments.created desc
//	;");
//if(empty($_GET['']))
$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
$length = 30;
$offect = ($page - 1) * $length;
$sql = sprintf('select 
	comments.*,
	posts.title as post_title
	from comments
	INNER JOIN posts on comments.post_id = posts.id
	order by comments.created desc
	limit %d , %d;',$offect,$length);

$comments = xiu_fetch_all($sql);
$total =xiu_fetch_one("select 
	count(1) as count
	from comments
	INNER JOIN posts on comments.post_id = posts.id")['count'];
$totalPages = (int)ceil($total / $length);

//因为网络之间传输的只能是字符串，所以我们先将数据转换成字符串
$json = json_encode(array(
	'totalPages' => $totalPages,
	'comments' => $comments
));   //序列化
header('Content-Type: application/json');
echo $json;
