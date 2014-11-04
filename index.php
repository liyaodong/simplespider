<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>xxx网站数据导入</title>
</head>
<body>
<?php
// 加密访问   防止别人访问到建议设置长点
$key = 'yourtoken';
$path = './';  //设置你的spider的目录
if(isset($_GET['key'])){
  if($_GET['key'] == $key) {
    ?>
    <h2 id="status">数据正在导入中，请稍后...</h2>
    <script src="http://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js"></script>
    <script>
      $(function(){
        $.ajax({
          url: "<?php echo $path;?>spider.php",
          type: 'GET',
          data: {key: '<?php echo $key;?>'},
        })
        .done(function(data) {
          if(data == 0){
            $('#status').text('此次没有文章更新可供导入');
          } else {
            $('#status').html('已导入'+ data +'条文章，详细信息请点击<a href="<?php echo $path;?>log.txt">这里</a>查看！');
          }
        })
        .fail(function() {
          $('#status').html('因网络原因导入失败，请联系<a href="xxx@xxx.com">xxx@xxx.com</a>');
        });

      });
    </script>
    <?php
  } else {
    echo "you don't have permission to access";
  }
} else {
    echo "you don't have permission to access";
}
?>
</body>
</html>