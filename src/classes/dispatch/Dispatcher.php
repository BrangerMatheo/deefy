<?php

namespace iutnc\deefy\dispatch;
class Dispatcher{

    private string $action;
    private $actions = [];

    public function __construct(){
        $this->action = $_GET['action'] ?? 'default';
    }

    public function addRoute(string $action, string $class) : self{
        $this->actions[$action] = $class;
        return $this;
    }

    public function run(): void{
        $class = $this->actions[$this->action] ?? null;
        if($class === null){
            $html = "<p>Cette page n'existe pas. <a href=\"?action=default\">Retour à l'accueil</a></p>";
        } else {
            $action = new $class();
            $html = $action->execute();
        }
        $this->renderPage($html);
    }

    private function navLink(string $action, string $label, string $icon): string{
        $current = ($this->action === $action || ($action === 'playlist' && $this->action === 'add-track'))
            ? ' aria-current="page"' : '';
        return '<a href="?action=' . $action . '"' . $current . '>' . $icon . '<span>' . $label . '</span></a>';
    }

    private function renderPage(string $html): void{
        $svg = fn(string $path) => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';

        $nav = $this->navLink('default', 'Accueil', $svg('<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>'))
             . $this->navLink('playlist', 'Mes playlists', $svg('<path d="M3 6h13M3 12h13M3 18h8"/><path d="M19 15V5l3 1"/><circle cx="17" cy="17" r="2.5"/>'))
             . $this->navLink('add-playlist', 'Nouvelle playlist', $svg('<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>'))
             . $this->navLink('add-user', 'Inscription', $svg('<circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 4-6 8-6s7 2 8 6"/>'));

        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deefy</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app">
    <aside class="side">
        <a class="brand" href="?action=default">
            <span class="brand-mark"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3z"/></svg></span>
            <span>Deefy</span>
        </a>
        <nav class="nav">{$nav}</nav>
    </aside>

    <main class="main">
        {$html}
    </main>

    <footer class="player" id="player">
        <div class="now">
            <div class="thumb"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3z"/></svg></div>
            <div class="txt">
                <span class="t" id="now-title">Aucune piste</span>
                <span class="a" id="now-artist">Choisissez une piste à lire</span>
            </div>
        </div>
        <div class="ctrl">
            <div class="btns">
                <button type="button" id="btn-prev" aria-label="Piste précédente"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M6 6h2v12H6zM9.5 12L18 18V6z"/></svg></button>
                <button type="button" id="btn-play" class="main-btn" aria-label="Lecture / pause">
                    <svg class="ico-play" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                    <svg class="ico-pause" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>
                </button>
                <button type="button" id="btn-next" aria-label="Piste suivante"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 6h2v12h-2zM6 18l8.5-6L6 6z"/></svg></button>
            </div>
            <div class="seek">
                <span id="cur">0:00</span>
                <input type="range" id="seek" min="0" max="100" step="0.1" value="0" aria-label="Position dans la piste">
                <span id="dur">0:00</span>
            </div>
        </div>
        <div class="vol">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 5L6 9H2v6h4l5 4z"/><path d="M15.5 8.5a5 5 0 0 1 0 7"/></svg>
            <input type="range" id="vol" min="0" max="1" step="0.01" value="0.8" aria-label="Volume">
        </div>
        <audio id="audio" preload="none"></audio>
    </footer>
</div>
<script src="assets/player.js"></script>
</body>
</html>
HTML;
    }
}
