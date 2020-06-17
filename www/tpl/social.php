<?php
function social(Round $r):string{
    $social="";

    if (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS']=="off"){
        $protocol="http://";
    }
    else{
        $protocol="https://";
    }

    $url= $protocol.$_SERVER['SERVER_NAME'].pathinfo ( $_SERVER["PHP_SELF"] ,  PATHINFO_DIRNAME )."/round.php?r=".$r->getCode();
    $url = rawurlencode($url);
    $text = "Partagez vos scores au jeu ".$r->getGameName();
    $text = urlencode($text);
    $linktwitter="https://twitter.com/intent/tweet?url=".$url."&text=".$text."&via=Minotaure&related=Minotaure";
    $linkfacebook="https://www.facebook.com/sharer/sharer.php?u=".$url."&t=".$text;
    $social="<div id='social'><a href='$linkfacebook' target='_blank'><img src='images/facebook.svg' alt='Logo Facebook.'></a> <a href='$linktwitter' target='_blank'><img src='images/twitter.svg' alt='Logo Twitter.'></a></div>";

    return $social;
}
