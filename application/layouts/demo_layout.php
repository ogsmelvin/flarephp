<!DOCTYPE html>
<html>
<head>
    <title><?= APP_TITLE ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= $uri->baseUrl ?>css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="<?= $uri->baseUrl ?>css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
</head>
<body>
    <div class="container well">
        <div class="row-fluid">
            <div class="span12">
            <?= $view->getContent() ?>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?= $uri->baseUrl ?>js/jquery.min.js"></script>
    <script type="text/javascript" src="<?= $uri->baseUrl ?>js/bootstrap.min.js"></script>
</body>
</html>