<?php 
require_once '../functions.php';
xiu_get_current_user();	

$size = 20 ;
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
if($page < 1){
	header('location: /admin/posts.php?page=1'.$search);
}

//处理分页参数

//$page = $page < 1 ? 1 : $page;


$where = '1 = 1';
$search = '';
//处理分类的筛选
if(isset($_GET['category'])&&$_GET['category']!=='all'){
	$where .= ' and posts.category_id='.$_GET['category'];
	$search .= '&category='.$_GET['category'];
	
}
if(isset($_GET['status'])&&$_GET['status']!=='all'){
	$where .= " and posts.status = '{$_GET['status']}'";
	$search .= '&status='.$_GET['status'];
	
	
}


$total_count = (int)xiu_fetch_one("select count(1) as num
	from posts
	inner join categories on posts.category_id = categories.id
	inner join users on posts.user_id = users.id
	where {$where} ;")['num'];
 $total_pages = ceil($total_count/$size);

//$page = $page > $total_pages ?  $total_pages : $page;
if($page > $total_pages){
	header('location: /admin/posts.php?page='.$total_pages.$search);
}




//var_dump($page);
//计算越过多少条数据
$offset = ($page - 1) * $size;
//查询全部数据
$posts = xiu_fetch_all("select
	  posts.id,
	  posts.title,
	  users.nickname as user_name,
	  categories.name as category_name,
	  posts.created,
	  posts.status
	from posts
	inner join categories on posts.category_id = categories.id
	inner join users on posts.user_id = users.id
	where {$where} 
	order by posts.created desc
	limit {$offset}, {$size};");
	
//获取分类的数据
$categories = xiu_fetch_all("select * from categories;");



//计算分页页码

$visiables = 5;
//左右范围区间
$region = ($visiables-1)/2;
$begin = $page - $region;
$end = $begin + $visiables;

//$begin 必须从一开始   >0
//$end 必须小于等于最大页码
if($begin<1){
	$begin = 1 ;
	$end = $begin + $visiables;
}

//$end 必须小于等于最大页码

 if($end > $total_pages + 1){
 	$end = $total_pages + 1;
	$begin = $end - $visiables;
	if($begin<1){
	$begin = 1 ;
}
 }
//转换状态的显示
function convert_status ($status){
	$dict = array(
	'published' => '已发布',
	'drafted' => '草稿',
	'trashed' => '回收站'
	);
	return isset($dict[$status]) ? $dict[$status] :'未知';
}

//
function convert_date($created){
	$timestamp = strtotime($created);
	return date('Y年m月d日 <b\r> H:i:s', $timestamp);
}
//function get_category($category_id){
//	return  xiu_fetch_one("select name from categories where id ={$category_id}")['name'];
//}
//
//function get_user($user_id){
//	return  xiu_fetch_one("select nickname from users where id ={$user_id}")['nickname'];
//}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>

    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach($categories as $item):?>
            <option value="<?php echo $item['id'] ?>" <?php echo isset($_GET['category'])&&$_GET['category']===$item['id']?' selected':'' ?>><?php echo $item['name'] ?></option>
            <?php endforeach;?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted" <?php echo isset($_GET['status'])&&$_GET['status']==='drafted'?' selected':'' ?>>草稿</option>
            <option value="published" <?php echo isset($_GET['status'])&&$_GET['status']==='published'?' selected':'' ?>>已发布</option>
            <option value="trashed" <?php echo isset($_GET['status'])&&$_GET['status']==='trashed'?' selected':'' ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
        	<?php if($page>1):?>
          <li><a href="?page=<?php echo $page-1?>">上一页</a></li>
          <?php endif;?>
          <?php for($i = $begin;$i<$end;$i++): ?>
          <li <?php echo $i==$page ? " class='active'" : "" ?> ><a href="?page=<?php echo $i.$search?>"><?php echo $i ?></a></li>
          <?php endfor; ?>
          <?php if($page<$end-1):?>
          <li><a href="?page=<?php echo $page+1?>">下一页</a></li>
          <?php endif;?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
        	<?php foreach($posts as $item): ?>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['title'] ?></td>
            <!--<td><?php //echo get_user($item['user_id']); ?></td>
            <td><?php //echo get_category($item['category_id']); ?></td>-->
            <td><?php echo $item['user_name']; ?></td>
            <td><?php echo $item['category_name']; ?></td>
            <td class="text-center"><?php echo convert_date($item['created']) ?></td>
            <td class="text-center"><?php echo convert_status($item['status']);?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/posts-delete.php?id=<?php echo $item['id']; ?>"  class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>