<?php
require_once '../functions.php';
xiu_get_current_user();
?>
<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8">
		<title>Comments &laquo; Admin</title>
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
					<h1>所有评论</h1>
				</div>
				<!-- 有错误信息时展示 -->
				<!-- <div class="alert alert-danger">
				<strong>错误！</strong>发生XXX错误
				</div> -->
				<div class="page-action">
					<!-- show when multiple checked -->
					<div class="btn-batch" style="display: none">
						<button class="btn btn-info btn-sm">
						批量批准
						</button>
						<button class="btn btn-warning btn-sm">
						批量拒绝
						</button>
						<button class="btn btn-danger btn-sm">
						批量删除
						</button>
					</div>
					<ul class="pagination pagination-sm pull-right" id="twbs-pagination">
						<!--<li><a href="#">上一页</a></li>
						<li><a href="#">1</a></li>
						<li><a href="#">2</a></li>
						<li><a href="#">3</a></li>
						<li><a href="#">下一页</a></li>-->
					</ul>
				</div>
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center" width="40">
								<input type="checkbox">
							</th>
							<th width="60">作者</th>
							<th>评论</th>
							<th>评论在</th>
							<th>提交于</th>
							<th >状态</th>
							<th class="text-center" width="150">操作</th>
						</tr>
					</thead>
					<tbody>
						<!--<tr class="danger">
						<td class="text-center"><input type="checkbox"></td>
						<td>大大</td>
						<td>楼主好人，顶一个</td>
						<td>《Hello world》</td>
						<td>2016/10/07</td>
						<td>未批准</td>
						<td class="text-center">
						<a href="post-add.html" class="btn btn-info btn-xs">批准</a>
						<a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
						</td>
						</tr>-->

					</tbody>
				</table>
			</div>
		</div>

		<?php $current_page = 'comments'; ?>
		<?php
		include 'inc/sidebar.php';
		?>
		<script id="comments_tmpl" type="text/x-jsrender">
			{{for comments}}
			<tr {{if status == 'held'}}  class="warning" {{else status == 'rejected'}} class="danger" {{/if}} data-id="{{:id}}">
			<td class="text-center">
			<input type="checkbox">
			</td>
			<td>{{:author}}</td>
			<td>{{:content}}</td>
			<td>{{:post_title}}</td>
			<td>{{:created}}</td>
			<td>{{:status}}</td>
			<td class="text-center">
			{{if status == 'held'}}
			<a href="post-add.html" class="btn btn-info btn-xs">
			拒绝
			</a>
			<a href="post-add.html" class="btn btn-info btn-xs">
			批准
			</a>
			{{/if}}
			<a href="javascript:;" class="btn btn-danger btn-xs btn-delete">
			删除
			</a></td>
			</tr>
			{{/for}}
		</script>
		<script src="/static/assets/vendors/jquery/jquery.js"></script>
		<script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
		<script src="/static/assets/vendors/jsrender/jsrender.js"></script>
		<script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>

		<script>//发送ajax请求数据

//	$.getJSON('/admin/api/comments.php',{page:3},function(res){
//		//渲染数据
//		var html = $('#comments_tmpl').render({
//		comments : res
//		})
////		console.log(html);
//		$('tbody').append(html);
//	});
var current_page = 1;

function loadPageData(page) {
	$.getJSON('/admin/api/comments.php', {
		page: page
	}, function(res) {
		totalPages = res.totalPages;
		if(page > totalPages) {
			loadPageData(totalPages);
			return false;
		}
		$('#twbs-pagination').twbsPagination('destroy');
		$('#twbs-pagination').twbsPagination({
			totalPages: totalPages,
			visiablePages: 5,
			initiateStartPageClick: false,
			startPage: page,
			first: '首頁',
			last: '尾頁',
			prev: '上一页',
			next: '下一页',
			onPageClick: function(e, page) {
				//			console.log(e);
				console.log(page);
				loadPageData(page);
			}
		});
		var html = $('#comments_tmpl').render({
			comments: res.comments
		})
		$('tbody').fadeOut(function() {
			$(this).html(html).fadeIn();
			current_page = page;
		})

	});
}
//	$('#twbs-pagination').twbsPagination({
//			totalPages: 100,
//			visiablePages: 5,
//			first: '首頁',
//			last: '尾頁',
//			prev: '上一页',
//			next: '下一页',
//			onPageClick: function(e, page) {
//				//			console.log(e);
//				console.log(page);
//				loadPageData(page);
//			}
//		});
loadPageData(1);

//删除功能的实现
//==========================

//在这里注册事件，时机不多，这样会注册不到事件，需要采用  ===事件委托===
//给 需要出发事件的 父元素 注册事件
//由于删除按钮是动态添加的，而且执行动态添加的代码是在 此处 的js 执行过后再执行的,过早注册 ，就注册不上
//	$('.btn-delete').on('click',function(){
//
//	});
$('tbody').on('click', '.btn-delete', function() {
	//删除单条数据的时机
	//1.先拿到需要删除数据的ID
	//		console.log(123);
	var $tr = $(this).parent().parent();
	var id = $tr.data('id');
	//2.发送哦一个ajax请求 告诉服务端 需要删除那一条数据
	//		console.log(id);
	$.get('/admin/api/comment-delete.php', {
		id: id
	}, function(res) {
		console.log(typeof res);
		if(!res) return;
		//3. 根据服务端的删除成功与否 决定是否在界面上展示这个元素（不合理）
		//			$tr.remove();
		//4.重新载入数据
		loadPageData(current_page);
	})

});</script>
		<script>NProgress.done()</script>

	</body>
</html>
