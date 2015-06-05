<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/2/2015
 * Time: 9:00 AM
 *
 * This page is for test emulation of ViewModels
*/
// require_once(__DIR__.'/lib/Tops/start/autoload.php');
// require_once(__DIR__.'/lib/Tops/start/init.php');
$testpage = $_GET['p'];
$vmcontent = __DIR__."/lib/test/vmtest/$testpage.html";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Test View Model</title>

    <link rel="stylesheet" href="/modules/system/system.base.css">
    <link rel="stylesheet" href="/modules/field/theme/field.css">
    <link rel="stylesheet" href="/modules/shortcut/shortcut.css">
    <link rel="stylesheet" href="/sites/all/themes/scymtheme/assets/css/scym-theme.min.css">
    <link rel="stylesheet" href="/sites/all/themes/scymtheme/assets/css/scym-more.css">

    <!-- HTML5 element support for IE6-8 -->
    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src='/sites/all/modules/jquery_update/replace/jquery/1.10/jquery.min.js'></script>
    <script src="/misc/jquery.once.js?v=1.2"></script>
    <script src="/misc/drupal.js?noae8u"></script>
    <script src="/modules/contextual/contextual.js?v=1.0"></script>
    <script src="/sites/all/modules/jquery_update/replace/ui/external/jquery.cookie.js?v=67fb34f6a866c40d0570"></script>
    <script src="/sites/all/modules/escape_admin/js/escapeAdmin.modernizer.js?v=7.37"></script>
    <script src="/sites/all/modules/escape_admin/js/escapeAdmin.js?v=7.37"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/knockout/3.3.0/knockout-min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
    <script src="/assets/js/Tops.Peanut/Peanut.js?v=1.0"></script>
    <script src="/assets/js/Tops.App/App.js?v=1.0"></script>
    <script src="/sites/all/modules/devel/devel_krumo_path.js?noae8u"></script>
    <script src="/modules/toolbar/toolbar.js?noae8u"></script>
    <script src="/sites/all/themes/scymtheme/assets/js/bootstrap.min.js?noae8u"></script>
    <script>jQuery.extend(Drupal.settings, {"basePath":"\/","pathPrefix":"","ajaxPageState":{"theme":"scymtheme","theme_token":"AnnrB3Lj3k1e08TcuVPWLcvdknMEj0oYxsbf0F5cLVE","js":{"assets\/js\/Tops.App\/TestPageViewModel.js":1,"0":1,"sites\/all\/themes\/bootstrap\/js\/bootstrap.js":1,"\/\/code.jquery.com\/jquery-1.10.2.min.js":1,"1":1,"misc\/jquery.once.js":1,"misc\/drupal.js":1,"modules\/contextual\/contextual.js":1,"sites\/all\/modules\/jquery_update\/replace\/ui\/external\/jquery.cookie.js":1,"sites\/all\/modules\/escape_admin\/js\/escapeAdmin.modernizer.js":1,"sites\/all\/modules\/escape_admin\/js\/escapeAdmin.js":1,"http:\/\/cdnjs.cloudflare.com\/ajax\/libs\/knockout\/3.3.0\/knockout-min.js":1,"assets\/js\/Tops.Peanut\/Peanut.js":1,"assets\/js\/Tops.App\/App.js":1,"sites\/all\/modules\/devel\/devel_krumo_path.js":1,"modules\/toolbar\/toolbar.js":1,"sites\/all\/themes\/scymtheme\/assets\/js\/bootstrap.min.js":1},"css":{"modules\/system\/system.base.css":1,"modules\/contextual\/contextual.css":1,"sites\/all\/modules\/escape_admin\/css\/escapeAdmin.toolbar.css":1,"sites\/all\/modules\/calendar\/css\/calendar_multiday.css":1,"sites\/all\/modules\/date\/date_api\/date.css":1,"sites\/all\/modules\/date\/date_popup\/themes\/datepicker.1.7.css":1,"sites\/all\/modules\/date\/date_repeat_field\/date_repeat_field.css":1,"modules\/field\/theme\/field.css":1,"sites\/all\/modules\/views\/css\/views.css":1,"sites\/all\/modules\/ckeditor\/css\/ckeditor.css":1,"sites\/all\/modules\/ctools\/css\/ctools.css":1,"modules\/shortcut\/shortcut.css":1,"modules\/toolbar\/toolbar.css":1,"sites\/all\/themes\/scymtheme\/assets\/css\/scym-theme.min.css":1,"sites\/all\/themes\/scymtheme\/assets\/css\/scym-more.css":1}},"currentPath":"node\/4","currentPathIsAdmin":false,"tableHeaderOffset":"Drupal.toolbar.height","bootstrap":{"anchorsFix":"1","anchorsSmoothScrolling":"1","popoverEnabled":"1","popoverOptions":{"animation":1,"html":0,"placement":"right","selector":"","trigger":"click","title":"","content":"","delay":0,"container":"body"},"tooltipEnabled":"1","tooltipOptions":{"animation":1,"html":0,"placement":"auto left","selector":"","trigger":"hover focus","delay":0,"container":"body"}}});</script>

    <!--  end drupal generated  -->

</head>

<body class="html not-front logged-in one-sidebar sidebar-second page-node page-node- page-node-4 node-type-page toolbar toolbar-drawer" >
<div class="main-container container">
    <header role="banner" id="page-header">
        <p class="lead">Society of Friends</p>
    </header> <!-- /#page-header -->

    <div class="row">
        <section class="col-sm-12">
            <a id="main-content"></a>
            <h1 class="page-header">Test Page</h1>
            <messages-component></messages-component>
            <div class="region region-content">
                <section id="block-system-main" class="block block-system clearfix">
                    <div id="node-4" class="node node-page clearfix" about="/TestPage" typeof="foaf:Document">
                        <span property="dc:title" content="Page Title" class="rdf-meta element-hidden"></span>
                        <span property="sioc:num_replies" content="0" datatype="xsd:integer" class="rdf-meta element-hidden"></span>
                        <div class="content">

                            <div class="field field-name-body field-type-text-with-summary field-label-hidden">
                                <div class="field-items">
                                    <div class="field-item even" property="content:encoded">

                                        <!-- ****************** begin view markup  ************************* -->
                                        <?php
                                        include($vmcontent);
                                        ?>
                                        <!-- ****************** end view markup  ************************* -->

                                    </div>
                                </div>
                            </div>
                        </div><!-- end content div -->
                    </div> <!-- node-4 -->
                </section> <!-- block-system-main -->
            </div> <!-- end region-conten div -->
        </section> <!-- end outer section -->
    </div> <!-- end row -->
</div> <!-- end main-container -->


<footer class="footer container">
</footer>

<!-- initialize and bind viewmodel -->
<script src="/assets/js/Tops.App/<?php print $testpage?>ViewModel.js?noae8u"></script>
<script>ViewModel.init('//', function() {  ko.applyBindings(ViewModel); });</script>


<!-- bootstrap template startup -->
<script src="/sites/all/themes/bootstrap/js/bootstrap.js?noae8u"></script>


</body>
</html>

