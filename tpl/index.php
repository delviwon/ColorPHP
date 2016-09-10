<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link href="<?php echo __PUBLIC__?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>




<div class="container-fluid">

    <div class="page-header">
        <h1>文件上传 <small>File Upload</small></h1>
    </div>

    <div class="row">
        <div class="col-sm-12">

            <form role="form" action="<?php echo get_url('index/upload')?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="image">缩略图</label>
                    <input name="image" type="file" id="image">
                </div>
                <button type="submit" class="btn btn-primary">立即上传</button>
            </form>

        </div>
    </div>

</div>





<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo __PUBLIC__?>bootstrap/js/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo __PUBLIC__?>bootstrap/js/bootstrap.min.js"></script>
</body>
</html>