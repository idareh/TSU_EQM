<!DOCTYPE html">
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <link type="text/css" rel="stylesheet" href="style.css">
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="login.js"></script>
	<script>
	
function login(){   
var username = document.myform.username.value;
var password = document.myform.password.value;
 
if(username==""){
$("#user-error").fadeIn(700).show("slow").html(
"<font style="margin-left:30px;color:red;font-size:12px;">\n\
กรุณากรอก username</font>"
);      
}
else{
$("#user-error").fadeOut(700).hide("slow");
}
 
if(password==""){
$("#pass-error").fadeIn(700).show("slow").html(
"<font style="margin-left:30px;color:red;font-size:12px;">\n\
กรุณากรอก password</font>"
);      
}
else {
$("#pass-error").fadeOut(700).hide("slow");
}
 
if(username!="" && password!=""){
var str = Math.random();
var datastring = 'str'+str + '&username='+username +
    '&password='+password;
$.ajax({
type:'POST',
url:'login_sql.php',
data:datastring,
 
success:function(data){
if(data==1){
$("#formlogin").html("<font size="5">success</font>");
//ประยุกต์ใช้ส่วนนี้สั่งโหลด profile ของ member แต่ละคนได้
// $("#formlogin").load("user_profile.php");
}
else{
$("#login_fail").html("<font color="red">Username หรือ\n\
 Password ไม่ถูกต้อง</font>");
document.myform.username.value="";
document.myform.password.value="";
}
}
});
}
 
}
	</script>
    </head>
    <body>
        
        <center>
<div class="content">
<table>
<tbody><tr>
<td>
<div id="formlogin">
<form name="myform" method="post" action="">
 
<div id="user-error"></div>
<label for="username" style="margin-left: 29px;">Username : </label>
<div>
<span><img style="margin-bottom:-4px;" src="images/user.png">
</span> <input id="username" name="username" style="" type="text">
</div>
 
<div class="cleaner_h5"></div>
 
<div id="pass-error"></div>
<label for="password" style="margin-left: 29px;">Password : </label>
<div>
<span><img style="margin-bottom:-4px;" src="images/unlocked.png">
</span> <input id="password" name="password" style="" type="password">
</div>
 
<div class="cleaner_h5"></div>
 
<div>
<input style="margin-left:28px;" name="check" id="check" 
       value="on" type="checkbox">Login ตลอดไป
</div>
 
<div class="cleaner_h5"></div>
<div>
<input id="submit" value="Login" onclick="login()" 
       style="float:right;background:#3B59A8;border:1px solid #000;
color:#ffffff;font-weight:bold;" type="button">
</div>
 
<div class="cleaner_h10"></div>
<center><div id="login_fail"></div></center>
</form>
</div>
</td>
</tr>
</tbody></table>
</div>
</center>
        
    </body>
</html>


