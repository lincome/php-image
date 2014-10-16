<?php
require_once 'CombineImage.class.php';

$ci = new CombineImage(array("1.png", "2.png"), "output.jpg");
$ci->combine();
$ci->show();
?>