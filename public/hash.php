<?php
$motDePasse = 'admin123';
$hash = password_hash($motDePasse, PASSWORD_DEFAULT);
echo $hash;
