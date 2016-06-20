<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>你好同学信息绑定</title>

    <!-- Bootstrap core CSS -->
      <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom styles for this template -->
      <link href="assets/css/cover.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
      <script src="assets/js/ie-emulation-modes-warning.js"></script>

  </head>
  <body>

  <div class="container">
    

<?
echo $_POST['学校年级专业'];
echo $_POST['性别'];
echo $_POST['OpenID'];


if( $_POST['学校年级专业'] && $_POST['性别'] && $_POST['OpenID'] ){
    
    $schoolGrade= $_POST['学校年级专业'];
    $sex = $_POST['性别'];
    $OpenID = $_POST['OpenID'];

}else{
    echo "<br/><div class=\"alert alert-danger\" role=\"alert\">什么鬼，绑定出错了 /(ㄒoㄒ)/~~</div><br/>";
    if(empty($_POST['学校年级专业'])) {
        echo "<h4>学校年级专业有误，请返回重新填写</h4><br>";
    
        echo "
              <div class=\"mastfoot\">
                <div class=\"inner\">
                <hr>
                <p>你好同学, Design by @Chokingwin.</p>
                </div>
              </div> 
             ";
        exit();
  }
    
}

/*连接数据库*/
$conn = mysql_connect('localhost','root','yuanzhenyuan2016.');
mysql_select_db('higirl',$conn);

$sql1 = "select OpenID from userInfo where OpenID = '$OpenID' ";
$rs = mysql_query($sql1,$conn);
$row = mysql_fetch_array($rs);
$result = $row[0];

if($result){
    echo "<br/><div class=\"alert alert-info\" role=\"alert\">n(*≧▽≦*)n</div><br/>";
    echo '<h4>同学，你已经注册过了。请忽略此次注册</h4>';
}else {
    $sql2 = "insert into userInfo(school,sex,OpenID) values('$schoolGrade','$sex','$OpenID')";
    $rs = mysql_query($sql2,$conn);
 
    if($rs)
    {
        echo "<br/><div class=\"alert alert-success\" role=\"alert\">n(*≧▽≦*)n</div><br/>";
        echo '<h4>绑定成功 </h4>';
    }
    else 
    {
        echo "<br/><div class=\"alert alert-danger\" role=\"alert\">什么鬼，绑定出错了 /(ㄒoㄒ)/~~</div><br/>";
        echo '<h4>绑定失败了.</h4><br><h4>客官回去填写信息，再提交一次吧...</h4><br>';
        echo mysql_error();
    }        

}




?>
    
    <div class="mastfoot">
        <div class="inner">
        <hr>
          <p>你好同学, Design by @Chokingwin.</p>
        </div>
      </div> 

    </div>

   <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    </body>
  </html>
