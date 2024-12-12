<!DOCTYPE HTML>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>アンケート</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="form-header">
        <h1>アンケートフォーム</h1>
        <p>以下の質問にお答えください。</p>
    </div>

    <form method="post" action="index.php">
        <h1>➀ どの学年の子どもが最も多く入塾していますか？</h1>
        <br>
        <?php
        // 学年の選択肢➀
        $age = array(
            '５歳以下',
            '１年生',
            '２年生',
            '３年生',
            '４年生以上'
        );

        // ラジオボタンの生成
        for ($i = 0; $i < count($age); $i++) {
            echo "<input type='radio' name='cn1' value='$i' required>{$age[$i]}<br>\n";
        }
        ?>
        <br>

        <!-- 2つ目の質問 -->
        <h1>➁ 退塾する子どもが多い学年はどの学年ですか？</h1>
        <br>
        <?php
        // 学年の選択肢➁
        $age_exit = array(
            '５歳以下',
            '１年生',
            '２年生',
            '３年生',
            '４年生以上'
        );

        // ラジオボタンの生成（2番目の質問）
        for ($i = 0; $i < count($age_exit); $i++) {
            echo "<input type='radio' name='cn2' value='$i' required>{$age_exit[$i]}<br>\n";
        }
        ?>
        <br>
        <input type="submit" name="submit" value="送信">
    </form>

    <?php
    // アンケートデータのファイル
    $file_path = 'enquete.txt';

    // 入塾学年の選択肢
    $age = array(
        '５歳以下',
        '１年生',
        '２年生',
        '３年生',
        '４年生以上'
    );

    // 退塾学年の選択肢
    $age_exit = array(
        '５歳以下',
        '１年生',
        '２年生',
        '３年生',
        '４年生以上'
    );

    // ファイルが存在しない場合、初期化
    if (!file_exists($file_path)) {
        $fp = fopen($file_path, 'w');
        for ($i = 0; $i < count($age); $i++) {
            fwrite($fp, "0\n"); // 初期値0
        }
        for ($i = 0; $i < count($age_exit); $i++) {
            fwrite($fp, "0\n"); // 初期値0（退塾）
        }
        fclose($fp);
    }

    // ファイルの読み込み
    $ed = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // `$age` の要素数と `$ed` の要素数を一致させる
    for ($i = 0; $i < count($age) + count($age_exit); $i++) {
        if (!isset($ed[$i])) {
            $ed[$i] = 0; // 要素が不足している場合は0で補う
        }
    }

    // POSTされた場合の処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        // ➀の質問の処理
        if (isset($_POST['cn1'])) {
            $selected1 = (int)$_POST['cn1'];
            if (isset($ed[$selected1])) {
                $ed[$selected1]++; // ➀回答数を加算
            }
        }
        // ➁の質問の処理
        if (isset($_POST['cn2'])) {
            $selected2 = (int)$_POST['cn2'];
            if (isset($ed[$selected2 + count($age)])) {
                $ed[$selected2 + count($age)]++; // ➁回答数を加算
            }
        }

        // ファイルに保存
        $fp = fopen($file_path, 'w');
        foreach ($ed as $votes) {
            fwrite($fp, $votes . "\n");
        }
        fclose($fp);

        // フォーム送信後、リダイレクトして再送信を防ぐ
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    ?>

    <h2>回答結果➀ - どの学年の子どもが最も多く入塾していますか？</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>学 年</th>
            <th>回答数</th>
            <th>グラフ</th>
        </tr>

        <?php
        // 学年ごとの集計結果
        $total_votes = 0;
        for ($i = 0; $i < count($age); $i++) {
            $votes = (int)$ed[$i]; // 数値として扱う
            $bar_width = $votes * 15; // グラフの幅を計算
            echo "<tr>";
            echo "<td>{$age[$i]}</td>";
            echo "<td>{$votes} </td>";
            echo "<td><div class='graph-1' style='width:{$bar_width}px;'></div></td>";
            echo "</tr>\n";
            $total_votes += $votes; // 合計の計算
        }

        // 合計を表示
        echo "<tr>
        <td colspan='2' style='text-align: right; font-weight: bold;'>合  計</td>
        <td>{$total_votes} </td>
        </tr>";
        ?>

    </table>

    <h3>回答結果➁ - 退塾する子どもが多い学年はどの学年ですか？</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>学 年</th>
            <th>回答数</th>
            <th>グラフ</th>
        </tr>

        <?php
        // 退塾学年ごとの集計結果
        $total_votes_exit = 0;
        for ($i = 0; $i < count($age_exit); $i++) {
            $votes = (int)$ed[$i + count($age)]; // 数値として扱う
            $bar_width = $votes * 15; // グラフの幅を計算
            echo "<tr>";
            echo "<td>{$age_exit[$i]}</td>";
            echo "<td>{$votes} </td>";
            echo "<td><div class='graph-2' style='width:{$bar_width}px;'></div></td>";
            echo "</tr>\n";
            $total_votes_exit += $votes; // 合計の計算
        }

        // 合計を表示
        echo "<tr>
        <td colspan='2' style='text-align: right; font-weight: bold;'>合  計</td>
        <td>{$total_votes_exit} </td>
        </tr>";
        ?>

    </table>
</body>

</html>