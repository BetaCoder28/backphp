<?php

class YoutubeControlador {
    static $cursosArray = [
        'https://www.youtube.com/watch?v=iX_on3VxZzk', //red neuronal
        'https://youtu.be/zNmDOXbTugE?si=K8Ni7pBaCPk0NSJB', //flutter
        'https://youtu.be/TjaG7243BF0?si=Es-QcQA9wVRbX-uc',//develoteca python
        'https://youtu.be/rLoWMU4L_qE?si=g6bnQESTIYsS3IP3',//react
        'https://youtu.be/axHut2e84fc?si=xm6xX5njeo2iozUF'//c$
    ];
    
    static public function GetVideo(){
        echo json_encode([

            "status" => 200,
            "cursos" => self::$cursosArray
            ]
        ); 
    }

    static public function GetVideoByIndex($index){

        if(isset(self::$cursosArray[$index])) {
            echo json_encode([
                "status" => 200,
                "curso" => self::$cursosArray[$index]
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