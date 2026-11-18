<?php

namespace iutnc\deefy\repository;

use Exception;
use PDO;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class DeefyRepository
{
    private PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf)
    {
        $dsn = $conf['driver'] . ':host=' . $conf['host'] . ';dbname=' . $conf['database'] . ';charset=utf8mb4';
        $this->pdo = new PDO($dsn, $conf['username'], $conf['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    /**
     * Charge le fichier de configuration (format ini) une seule fois au démarrage.
     */
    public static function setConfig(string $file): void
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new Exception("Erreur de lecture du fichier de configuration : $file");
        }
        foreach (['driver', 'host', 'database', 'username', 'password'] as $cle) {
            if (!array_key_exists($cle, $conf)) {
                throw new Exception("Paramètre manquant dans $file : $cle");
            }
        }
        self::$config = $conf;
        self::$instance = null; // force la reconnexion avec la nouvelle config
    }

    public static function getInstance(): DeefyRepository
    {
        if (self::$instance === null) {
            if (empty(self::$config)) {
                throw new Exception("Configuration absente : appelez DeefyRepository::setConfig() d'abord");
            }
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    /**
     * Permet d'injecter une connexion PDO existante (tests).
     */
    public static function setInstanceFromPdo(PDO $pdo): void
    {
        $r = (new \ReflectionClass(self::class))->newInstanceWithoutConstructor();
        $r->pdo = $pdo;
        self::$instance = $r;
    }

    // ------------------------------------------------------------------
    // 1. Liste des playlists (sans leurs pistes)
    // ------------------------------------------------------------------
    public function findAllPlaylists(): array
    {
        $stmt = $this->pdo->query('SELECT id, nom FROM playlist ORDER BY id');
        $playlists = [];
        foreach ($stmt->fetchAll() as $row) {
            $pl = new Playlist($row['nom']);
            $pl->setId((int)$row['id']);
            $playlists[] = $pl;
        }
        return $playlists;
    }

    /**
     * Retourne une playlist avec ses pistes, ou null si elle n'existe pas.
     */
    public function findPlaylistById(int $id): ?Playlist
    {
        $stmt = $this->pdo->prepare('SELECT id, nom FROM playlist WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        $stmt = $this->pdo->prepare(
            'SELECT t.* FROM track t
             INNER JOIN playlist2track p2t ON p2t.id_track = t.id
             WHERE p2t.id_pl = ?
             ORDER BY p2t.no_piste_dans_liste'
        );
        $stmt->execute([$id]);
        $pistes = [];
        foreach ($stmt->fetchAll() as $t) {
            $pistes[] = $this->hydraterPiste($t);
        }

        $pl = new Playlist($row['nom'], $pistes);
        $pl->setId((int)$row['id']);
        return $pl;
    }

    // ------------------------------------------------------------------
    // 2. Sauvegarde d'une playlist (vide de pistes)
    // ------------------------------------------------------------------
    public function savePlaylist(Playlist $playlist): Playlist
    {
        $stmt = $this->pdo->prepare('INSERT INTO playlist (nom) VALUES (?)');
        $stmt->execute([$playlist->nom]);
        $playlist->setId((int)$this->pdo->lastInsertId());
        return $playlist;
    }

    // ------------------------------------------------------------------
    // 3. Sauvegarde d'une piste
    // ------------------------------------------------------------------
    public function saveTrack(AudioTrack $track): AudioTrack
    {
        if ($track instanceof AlbumTrack) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO track (titre, genre, duree, filename, type, artiste_album, titre_album, annee_album, numero_album)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $track->titre, $track->genre, $track->duree, $track->fichier, 'A',
                $track->artiste, $track->album, $track->annee, $track->numero,
            ]);
        } elseif ($track instanceof PodcastTrack) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO track (titre, genre, duree, filename, type, auteur_podcast, date_podcast)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $track->titre, $track->genre, $track->duree, $track->fichier, 'P',
                $track->auteur, $track->date,
            ]);
        } else {
            throw new Exception('Type de piste non géré : ' . get_class($track));
        }
        $track->setId((int)$this->pdo->lastInsertId());
        return $track;
    }

    // ------------------------------------------------------------------
    // 4. Ajout d'une piste existante à une playlist existante
    // ------------------------------------------------------------------
    public function addTrackToPlaylist(int $idPlaylist, int $idTrack): void
    {
        $stmt = $this->pdo->prepare('SELECT COALESCE(MAX(no_piste_dans_liste), 0) + 1 FROM playlist2track WHERE id_pl = ?');
        $stmt->execute([$idPlaylist]);
        $numero = (int)$stmt->fetchColumn();

        $stmt = $this->pdo->prepare('INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (?, ?, ?)');
        $stmt->execute([$idPlaylist, $idTrack, $numero]);
    }

    // ------------------------------------------------------------------
    // Utilitaire : ligne SQL -> objet piste
    // ------------------------------------------------------------------
    private function hydraterPiste(array $t): AudioTrack
    {
        if ($t['type'] === 'P') {
            $piste = new PodcastTrack($t['titre'], (string)$t['filename']);
            $piste->setAuteur((string)$t['auteur_podcast']);
            $piste->setDate((string)$t['date_podcast']);
        } else {
            $piste = new AlbumTrack($t['titre'], (string)$t['filename'], (string)$t['titre_album'], (int)$t['numero_album']);
            $piste->setArtiste((string)$t['artiste_album']);
            $piste->setAnnee((int)$t['annee_album']);
        }
        $piste->setDuree((int)$t['duree']);
        $piste->setGenre((int)$t['genre']);
        $piste->setId((int)$t['id']);
        return $piste;
    }
}
