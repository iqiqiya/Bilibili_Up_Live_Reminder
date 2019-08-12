<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function curl($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:21.0) Gecko/20100101 Firefox/21.0');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 0);
    curl_setopt($curl, CURLOPT_HTTPGET, 0);
    //curl_setopt($curl, CURLOPT_COOKIEFILE, $this->_cookie); // 如果是需要登陆才能采集的话,需要加上你登陆后得到的cookie文件
    curl_setopt($curl, CURLOPT_TIMEOUT, 0); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0); // 在发起连接前等待的时间，如果设置为0，则无限等待。
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 0); // 尝试连接等待的时间，以毫秒为单位。如果设置为0，则无限等待。
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $tmpInfo = curl_exec($curl);
    //echo($tmpInfo);
    return $tmpInfo;
}
function sendMail($title,$content,$altcontent){
    require './PHPMailer-6.0.7/src/Exception.php';
    require './PHPMailer-6.0.7/src/PHPMailer.php';
    require './PHPMailer-6.0.7/src/SMTP.php';
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        //服务器配置
        $mail->CharSet ="UTF-8";                     //设定邮件编码
        $mail->SMTPDebug = 0;                        // 调试模式输出
        $mail->isSMTP();                             // 使用SMTP
        $mail->Host = 'smtp.qq.com';                // SMTP服务器
        $mail->SMTPAuth = true;                      // 允许 SMTP 认证
        $mail->Username = '*********@qq.com';                // SMTP 用户名  即邮箱的用户名
        $mail->Password = 'oxwtyzbyxifqbgif';             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
        $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
        $mail->Port = 465;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

        $mail->setFrom('*********@qq.com', '直播小助手');  //发件人
        $mail->addAddress('iqiqiya@outlook.com', 'iqiqiya');  // 收件人
        //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
        $mail->addReplyTo('*********@qq.com', '直播小助手'); //回复的时候回复给哪个邮箱 建议和发件人一致
        //$mail->addCC('cc@example.com');                    //抄送
        //$mail->addBCC('bcc@example.com');                    //密送

        //发送附件
        // $mail->addAttachment('../xy.zip');         // 添加附件
        // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名

        //Content
        $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
        //$mail->Subject = 'bilibili直播提醒：Akie开播了' . time();
        $mail->Subject = $title;
        //$mail->Body    = '<h1>你最喜欢的up主正在直播中~</h1><br>当前时间：' . date('Y-m-d H:i:s');
        $mail->Body    = $content;
        $mail->AltBody = $altcontent;

        $mail->send();
        return '邮件发送成功';
    } catch (Exception $e) {
        return '邮件发送失败:'.$mail->ErrorInfo;
    }
}
//'*********@qq.com', '直播小助手'
//'iqiqiya@outlook.com', 'iqiqiya'
//'*********@qq.com', '直播小助手'
//'bilibili直播提醒：Akie开播了'
//'<h1>你最喜欢的up主Akie秋绘正在直播中~</h1>'
//'你最喜欢的up主Akie秋绘正在直播中~'
function getLiveStatus($LiveHomeUrl) {
    $contents = curl($LiveHomeUrl);
    //echo $contents;
    preg_match("~live_status\":(.*?),\"hidden_till~", $contents, $matches);
    if (count($matches) == 0) {
        echo '无法转换成相应的无水印图片，请换个链接试一下。';
        exit;
    }
    $live_status = $matches[1];
    //echo $live_status;    //输出img_url
    if ($live_status == 0){
        echo "Akie没有开播";
        //var_dump(sendMail('iqiqiya@outlook.com','Akie不在线','秋绘没有直播'));
        //var_dump(sendMail('Akie在摸鱼','<h1>你最喜欢的up主Akie秋绘暂时没有开播~</h1><br>','你最喜欢的up主Akie秋绘暂时没有开播~'));
    }
    else {
        echo "Akie开播了";
        // 调用发送方法，并在页面上输出发送邮件的状态
        var_dump(sendMail('bilibili直播提醒：Akie开播了','<h1>你最喜欢的up主Akie秋绘正在直播中~</h1><br><h2>直播间地址：https://live.bilibili.com/870691</h2>','你最喜欢的up主Akie秋绘正在直播中~   直播间地址：https://live.bilibili.com/870691'));
    }
}
$Live_Home_Url = "https://live.bilibili.com/870691";
getLiveStatus($Live_Home_Url);
exit;
?>