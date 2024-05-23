<?php
$icons = [
    ['url' => 'page1', 'label' => 'Page 1'],
    ['url' => 'page2', 'label' => 'Page 2'],
    ['url' => 'page3', 'label' => 'Page 3']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" />
    <title>トップ画面</title>
    <script>
        function goToPage(url) {
            window.location.href = url;
        }
    </script>
</head>
<body>
    <?php require 'db-connect.php'; ?>
    <div class="sideber">
        <?php foreach ($icons as $icon): ?>
            <div class="icon" onclick="goToPage('<?php echo $icon['url']; ?>')" title="<?php echo $icon['label']; ?>"></div>
        <?php endforeach; ?>
    </div>
    <div class="main-content">
        <?php
        ?>
    </div>
    <div class="sideber2">
        <p>トップ画面だよ</p>
    </div>
</body>
</html>
