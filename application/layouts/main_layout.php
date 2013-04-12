<!DOCTYPE html>
<html>
<head>
    <title><?= APP_TITLE ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= $uri->getBaseUrl() ?>assets/css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="<?= $uri->getBaseUrl() ?>assets/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
</head>
<body>
    <?= $content ?>
    <script type="text/javascript" src="<?= $uri->getBaseUrl() ?>assets/adk/js/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="<?= $uri->getBaseUrl() ?>assets/js/bootstrap.min.js"></script>
</body>
</html>