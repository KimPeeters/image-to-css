<?php
require_once dirname(__FILE__).'/../lib/imgToCss.php';

$imgToCss = new ImgToCss();
$imgToCss->addDirectory(dirname(__FILE__).'/testimg');
$styles = $imgToCss->run();
$map = $imgToCss->getMap();

?>
<html>
<head>
    <style>
        <?php echo $styles; ?>
    </style>
</head>
<body>

<p>
    Classes are prefixed by "img-".<br />
    Check the mapping for filename to classname conversion.
</p>

<p>&nbsp;</p>

<p>
    Css content:
    <pre>
<?php echo $styles; ?>
    </pre>
</p>
<p>&nbsp;</p>
<p>
    <pre>&lt;span class="img-online-marketing"&gt;&lt;/span&gt;</pre>
    <span class="img-online-marketing"></span>
</p>
<p>
    <pre>&lt;span class="img-ovide7"&gt;&lt;/span&gt;</pre>
    <span class="img-ovide7"></span>
</p>
<p>
Map:
<pre>
<?php
print_r($map);
?>
</p>
</pre>
</body>
</html>
