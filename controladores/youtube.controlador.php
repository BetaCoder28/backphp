<?php

class YoutubeControlador {
    static private $videosFile = __DIR__ . '/../videos.json';
    
    static private function getVideosArray() {
        if (!file_exists(self::$videosFile)) {
            return []; // archivo no encontrado
        }

        $json = file_get_contents(self::$videosFile);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return []; // error al parsear
        }

        if (isset($data['videos']) && is_array($data['videos'])) {
            return $data['videos'];
        }

        return [];
    }

    
    static public function GetVideo(){
        $videosArray = self::getVideosArray();
        echo json_encode([
            "status" => 200,
            "cursos" => $videosArray
        ]); 
    }

    static public function GetVideoByIndex($index){
    $videosArray = self::getVideosArray();

    if (isset($videosArray[$index]) && !empty($videosArray[$index])) {
        $url = $videosArray[$index];

        // Extraer ID de YouTube
        preg_match('/(?:v=|\/)([0-9A-Za-z_-]{11})(?:\?|&|$)/', $url, $matches);
        $id = $matches[1] ?? null;

        echo json_encode([
            "status" => 200,
            "curso" => [
                "url" => $url,
                "id" => $id
            ]
        ]);
    } else {
        echo json_encode([
            "status" => 404,
            "message" => "Curso no encontrado"
        ]);
    }
}




}
?>