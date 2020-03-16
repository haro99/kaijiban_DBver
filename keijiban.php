<?php

    //セッション開始
    session_start();
    //データベースの接続
    $mysqli = new mysqli('IP', 'root', '', 'DB_NAME');
    //文字をutf8に設定する
    $mysqli->set_charset("utf8");

    //時間を日本に設定
    date_default_timezone_set('Asia/Tokyo');

    //初めて入るときに書き込み失敗や入力がありませんと表示しないように
    if(isset($_POST['OK']))
    {
        //連投防止のためにセクションで管理
        if($_SESSION['key']==$_POST['key'])
        {
            //名前とコメントが空白を確認
            if(empty($_POST['comment']))
            {
                $messege = "<p>入力がありません、名前かコメントを入力してください</p>";

            }
            else
            {
                if(empty($_POST['myname'])) $myname = 'かかし';
                else $myname = $_POST['myname'];
                $time = date('Y/m/d H:i:s');
                $comment = $_POST['comment'];

                //エラー場所commentカラムがならない
                $sql = "INSERT INTO comments(
                    number, name, time, comment
                ) VALUES (
                    '', '$myname', '$time', '$comment'
                )";

                $mysqli->query($sql);
            }
        }
        else
        {
            $messege = "<p>書き込みに失敗しました</p>";
        }
    }
    // タイムスタンプと推測できない文字列にてキーを発行
    $key = md5(time()."推測できない文字列");
    // 発行したキーをセッションに保存
    $_SESSION['key'] = $key;
?>
<!DOCTYPE html>
<html laong="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="sample.css">
    <title>普通の掲示板</title>
</head>
<body>
    <div class="top"><h1>掲示板</h1></div>
    <?php if (isset($messege)) echo $messege; ?>
    <div class="top-comments">
    <?php

        $sql = "SELECT * FROM comments";
        $result = $mysqli -> query($sql);

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            echo "<div class=\"comments\">\n";
            echo "<p>". $row['number']. ", ". $row['name']. " ". $row['time']. "</p>";
            echo "<p>". $row['comment']. "</p>";
            echo "</div>\n";
        }
    ?>
    </div>
    <div align="left">
        <form action="keijiban.php" method="POST">
        <p>名前:<input type="" name="myname" value="" placeholder="お名前"></p>
        <p>コメント:<textarea name="comment" rows="4" cols="50" wrap="off"></textarea></p>
        <input type="hidden" name="key" value="<?= $key; ?>" />
        <span>
            <input type="submit" name="OK" value="OK">
        </span>
        </form>
    </div>

</body>
</html>
<?php $mysqli->close(); ?>
