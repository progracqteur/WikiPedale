<?php
$nonce = base64_decode('alZweFQxQ1YwU2w1QnEwRlNVVk1EMnJzNVJjPQ==');
echo "nonce decodé : ".$nonce."\n";
$created= '2012-08-30T00:42:56-22:00';
$secret = "fill-with-password";
$nonce= base64_encode($nonce);
$expected = base64_encode(sha1(base64_decode($nonce).$created.$secret, true));
echo $expected;
echo "\n";
?>
