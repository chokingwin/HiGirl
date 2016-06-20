<?
  $OpenID = $_GET[OpenID];
?>

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

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
      .left {
        text-align:left
      }
    </style>
  </head>
  <body>

  <div class="container">

      <div>
        <h2>信息绑定 O(∩_∩)O</h2>
      </div>
      <form class="" role="form" name="info" action="addInfo.php" method="post">
        
        <div class="left">
          <div class="form-group">
            <label for="性别">性别：</label>  
            <input class="am-radio-inline" type="radio" name="性别" id="性别" value="男" size="" checked />男&nbsp;&nbsp;
            <input class="am-radio-inline" type="radio" name="性别" id="性别" value="女" size="" />女
          </div>
          <div class="form-group">
            <label for="学校年级专业">学校年级专业：(例如：北理猪12计算机)</label>    
            <input type="text" class="form-control" name="学校年级专业" id="学校年级专业" placeholder=""/>  
          </div>
        </div>

        <div class="form-group" style="VISIBILITY:hidden">
        <?
          echo "<input class=\"form-control\" type=\"text\"  name=\"OpenID\" value=\"".$OpenID."\">";
        ?>
        </div>  
        <button type="submit" class="btn btn-lg btn-success" >提交绑定</button>

      </form>      

      <div class="mastfoot">
        <div class="inner">
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