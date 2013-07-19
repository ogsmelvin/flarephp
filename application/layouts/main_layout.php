<!DOCTYPE html>
<html>
<head>
    <title><?= APP_TITLE ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= $uri->getBaseUrl() ?>css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="<?= $uri->getBaseUrl() ?>css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
</head>
<body>
    <div class="container well">
        <div class="row">
            <?= $view->getContent() ?>
        </div>
    </div>
    <script type="text/javascript" src="<?= $uri->getBaseUrl() ?>js/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="<?= $uri->getBaseUrl() ?>js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?= $uri->getBaseUrl() ?>js/flare.js"></script>
</body>
</html>