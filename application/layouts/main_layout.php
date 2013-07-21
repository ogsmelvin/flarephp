<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?= APP_TITLE ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Flare PHP Documentation">
        <meta name="author" content="Anthony De Leon">
        <link href="/css/bootstrap.css" rel="stylesheet">
        <link href="/css/bootstrap-responsive.css" rel="stylesheet">
        <link href="/css/bootstrap-doc.css" rel="stylesheet">
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="/ico/apple-touch-icon-57-precomposed.png">
        <link rel="shortcut icon" href="/ico/favicon.png">
        <!--[if lt IE 9]>
          <script src="/js/html5shiv.js"></script>
        <![endif]-->
    </head>
    <body>
        <header>
            <div class="navbar navbar-inverse navbar-fixed-top">
                <div class="navbar-inner">
                    <div class="container">
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a>
                        <a class="brand" href="/index.html">Documentation</a>
                        <div class="nav-collapse collapse">
                            <ul class="nav pull-right">
                                <li><a href="/">Home</a></li>
                                <li><a href="/">Tree</a></li>
                                <li><a href="/">Help</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <article class="container">
            <div class="row-fluid">
                <div class="span3">
                    <ul class="nav nav-tabs nav-stacked">
                        <li><a href="/content.html"><i class="icon-chevron-right pull-right"></i> Get Started</a></li>
                        <li><a href="/#"><i class="icon-chevron-right pull-right"></i> App Components</a></li>
                        <li><a href="/#"><i class="icon-chevron-right pull-right"></i> User Interfaces</a></li>
                        <li><a href="/#"><i class="icon-chevron-right pull-right"></i> Form Elements</a></li>
                        <li><a href="/#"><i class="icon-chevron-right pull-right"></i> Animation</a></li>
                        <li><a href="/#"><i class="icon-chevron-right pull-right"></i> Graphic</a></li>
                        <li><a href="/#"><i class="icon-chevron-right pull-right"></i> Example</a></li>
                        <li><a href="/#"><i class="icon-chevron-right pull-right"></i> Something else</a></li>
                    </ul>
                </div>
                <div class="span9">
                    <div class="doc-content-box">
                        <?= $view->getContent() ?>
                    </div>
                </div>
            </div>
        </article>
        <footer>
            <div class="footer">
                <div class="container">
                    <p>Copyright &copy; 2013. All rights reserved.</p>
                </div>
            </div>
        </footer>
        <script src="/js/jquery.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
    </body>
</html>