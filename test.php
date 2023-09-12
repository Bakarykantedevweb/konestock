<?php
// Définissez le timezone actuel à GMT
date_default_timezone_set('GMT');

// Obtenez la date actuelle au format Y-m-d
$date = date('Y-m-d');

// Rétablissez le timezone par défaut de votre application (par exemple, 'Europe/Paris')
date_default_timezone_set('Africa/Bamako'); // Remplacez 'Europe/Paris' par votre timezone par défaut

// Utilisez la date obtenue
echo $date;

