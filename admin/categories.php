<?php
require_once '../functions.php';
xiu_get_current_user();

//是否为需要编辑的数据
if (!empty($_GET['id'])) {
	//empty没有传递参数,返回普通表单,里面没有数据
	//!empty 客户端通过url传递了一个id,想要编辑分类内容,想要拿到一个已经数据的表单
	$current_edit_category = xiu_fetch_one('select * from categories where id = ' . $_GET['id']);
}
function add_caregory() {
	if (empty($_POST['name']) || empty($_POST['slug'])) {
		$GLOBALS['success'] = false;
		$GLOBALS['message'] = '请完整填写表单';
		return;
	}
	//接受并保存
	$name = $_POST['name'];
	$slug = $_POST['slug'];

	$rows = xiu_execute("insert into categories values(null,'{$slug}','{$name}');");

	//	if($rows<=0){
	//		$GLOBALS['message'] = '添加失败';
	//	}
	$GLOBALS['success'] = $rows <= 0 ? FALSE : TRUE;
	$GLOBALS['message'] = $rows <= 0 ? '添加失败' : '添加成功';

}

function edit_category() {
	global $current_edit_category;
	//编辑并点击保存提交才会执行
	//	if(empty($_POST['name'])||empty($_POST['slug'])){
	//		$GLOBALS['success'] = false;
	//		$GLOBALS['message'] = '请完整填写表单';
	//		return;
	//	}

	//接受并保存
	$name = empty($_POST['name']) ? $current_edit_category['name'] : $_POST['name'];
	//同步一下数据
	$current_edit_category['name'] = $name;
	$slug = empty($_POST['slug']) ? $current_edit_category['slug'] : $_POST['slug'];
	$current_edit_category['slug'] = $slug;
	$id = $current_edit_category['id'];
	$rows = xiu_execute("update categories set slug='{$slug}',name='{$name}' where id={$id}");

	//	if($rows<=0){
	//		$GLOBALS['message'] = '添加失败';
	//	}
	$GLOBALS['success'] = $rows <= 0 ? FALSE : TRUE;
	$GLOBALS['message'] = $rows <= 0 ? '更新失败' : '更新成功';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	//一旦表单提交请求,并且没有通过URL提交ID，就要向数据库添加资料
	if (empty($_GET['id'])) {
		add_caregory();
	} else {
		//有id
		edit_category();
	}

}

$categories = xiu_fetch_all('select * from categories;');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php
		include 'inc/navbar.php';
 ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
	      <?php if ($success): ?>
	      <div class="alert alert-success">
	        <strong>成功！</strong> <?php echo $message; ?>
	      </div>
	      <?php else: ?>
	      <div class="alert alert-danger">
	        <strong>错误！</strong> <?php echo $message; ?>
	      </div>
	      <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <?php if(isset($current_edit_category)):?>
          	<form action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $current_edit_category['id'] ?>" method='post'> 
            <h2>编辑《<?php echo $current_edit_category['name'] ?>》</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit_category['name']?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug"  value="<?php echo $current_edit_category['slug']?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>
          <?php else: ?>
      		<form action="<?php echo $_SERVER['PHP_SELF']?>" method='post'> 
		        <h2>添加新分类目录</h2>
		        <div class="form-group">
		          <label for="name">名称</label>
		          <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
		        </div>
		        <div class="form-group">
		          <label for="slug">别名</label>
		          <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
		          <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
		        </div>
		        <div class="form-group">
		          <button class="btn btn-primary" type="submit">添加</button>
		        </div>
          </form>
          <?php endif; ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a  id="btn_delete" class="btn btn-danger btn-sm" href="/admin/categories-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>

              	<?php foreach($categories as $item): ?>
             		<tr>
              		<td class="text-center"><input type="checkbox" data-id='<?php echo $item['id']; ?>'></td>
                	<td><?php echo $item['name']?></td>
                	<td><?php echo $item['slug']?></td>
                	<td class="text-center">
                  	<a href="/admin/categories.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                  	<a href="/admin/categories-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                	</td>
              	</tr>
              	<?php endforeach ?>
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php
		include 'inc/sidebar.php';
 ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>$(function($) {
	var $tbodyCheckboxs = $('tbody input');
	var $btn_delete = $('#btn_delete');
	//当表格中任意一个CheckBox状态发生变化时
	// version1.0=====================
	//		$tbodyCheckboxs.on('change',function(){
	//			var flag = false;
	//			$tbodyCheckboxs.each(function(i,item){
	//				//有任意一个CheckBox选中就显示,反之就隐藏
	//	  			if($(item).prop('checked')){
	//	  				flag = true;
	//	  			}
	//			})
	//
	//			flag ? $btn_delete.fadeIn() : $btn_delete.fadeOut();
	//		})
	var allChecks = [];
	$tbodyCheckboxs.on('change', function() {
		var id = $(this).data('id');

		if($(this).prop('checked')) {
			//				$('this').attr('data-id')
			//				$(this).data('id')
			//				$(this).prop('checked')
			//				allChecks.indexOf(id)==-1||allChecks.push(id);
			allChecks.includes(id) || allChecks.push(id);
		} else {
			allChecks.splice(allChecks.indexOf(id), 1);
		}
		console.log(allChecks);
		allChecks.length ? $btn_delete.fadeIn() : $btn_delete.fadeOut();
		$btn_delete.prop('search', '?id=' + allChecks)
	});
	$('thead input').on('change', function() {
		var hchecked = $(this).prop('checked');
		$tbodyCheckboxs.prop('checked', hchecked).trigger('change');

	});
})</script>
  <script>NProgress.done()</script>
</body>
</html>
