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
            $html = "<p>Action Pas Programmée</p>";
        } else {
            $action = new $class();
            $html = $action->execute();
        }
        $this->renderPage($html);
    }

    private function renderPage(string $html): void{
    echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>DEEFY</title>
        </head>
        <body>
            <header>
                <h1>DEEFY</h1>
                <nav>
                    <a href="?action=default">Accueil</a> |
                    <a href="?action=add-user">Inscription</a> |
                    <a href="?action=add-playlist">Créer une playlist</a> |
                    <a href="?action=playlist">Afficher la playlist</a>
                </nav>
            </header>
            <hr>
            <main>
                {$html}
            </main>
        </body>
        </html>
        HTML;
    }
}

