<?php

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\deefy\action\AddAlbumTrackAction;
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddUserAction;
use iutnc\deefy\repository\DeefyRepository;
session_start();

// configuration BD définie une seule fois au démarrage
DeefyRepository::setConfig(__DIR__ . '/db.config.ini');

$dispatcher = new Dispatcher();

$dispatcher
    ->addRoute('default', DefaultAction::class)
    ->addRoute('playlist', DisplayPlaylistAction::class)
    ->addRoute('add-playlist', AddPlaylistAction::class)
    ->addRoute('add-track', AddAlbumTrackAction::class)
    ->addRoute('add-user', AddUserAction::class);

$dispatcher->run();