<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Douban menus &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style>
  	tr,th{
  		text-align: center;
  	}
  	td>a{text-decoration: underline;}
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
  <?php include 'inc/navbar.php'; ?>
    
    <div class="container-fluid">
      <div class="page-title">
        <h1>导航菜单</h1>
        <img src="http://img7.doubanio.com/view/photo/s_ratio_poster/public/p2530591543.jpg" alt="" width="120">
      </div>
    	<h1 id="title">最新电影榜单（前十）</h1>
    	<hr />
      <!--<ul id="movies">-->
      	<table class="table table-striped table-bordered table-hover">
      		<thead>
      			<tr>
      				<!--<th class="text-center" width="40">
      					<input type="checkbox">
      				</th>-->
      				<th width="60">名字</th>
      				<th>题材</th>
      				<th>海报</th>
      				<th>主演1</th>
      				<th>主演2</th>
      				<th>导演</th>
      				<!--<th class="text-center" width="150">操作</th>-->
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
      </ul>
    </div>
  </div>

  <?php $current_page = 'douban'; ?>
  <?php include 'inc/sidebar.php'; ?>
	<script id="comments_tmpl" type="text/x-jsrender">
			{{for comments}}
			<tr>
			<td width="120">{{:title}}</td>
			<td>{{:genres}}</td>
			<td><img src="{{:images['small']}}" alt=""  width="120"/></td>
			<td><a href="{{:casts[0]['alt']}}">{{:casts[0]['name']}}</a></td>
			<td><a href="{{:casts[1]['alt']}}">{{:casts[1]['name']}}</a></td>
			<td width="120">{{:directors[0]['name']}}</td>
			</tr>
			{{/for}}
	</script>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <!--<script>
//	$.get('http://api.douban.com/v2/movie/in_theaters?count=10',{},function(data){
//		console.log(data);
//	});
		function foo(data){
			console.log(data);
		}
  </script>
  <script src="http://api.douban.com/v2/movie/in_theaters?count=10&callback=foo" ></script>-->
  <script>
  	$.ajax({
  		type:"get",
  		url:"http://api.douban.com/v2/movie/in_theaters?count=20",
  		dataType:'jsonp',
  		async:true,
  		success:function(data){
  			console.log(data);
  			$('#title').html(data.title);
//			$(data.subjects).each(
//				function(i,item){
//					//js手动渲染
////					$('#movies').append(`<li>${item.title}</li>`)
////					使用模板引擎渲染
//					
//					console.log(item);
//					
//				}
//			)
  			var html = $('#comments_tmpl').render({
					comments: data.subjects
					})
					$('tbody').fadeOut(function() {
					$(this).html(html).fadeIn();
					})
  		}
  	});
  </script>
  
  
  <script>NProgress.done()</script>
</body>
</html>
