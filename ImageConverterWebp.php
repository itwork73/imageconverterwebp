<?

// recursive convert images to webp: @itwork73
// version 2.1

Class ImageConverterWebp {

    private $params = [
        "WEBP_SKIP_EXIST"=>true,
        "WEBP_QUALITY"=>90,
        "LIMIT"=>false,
    ];

    private $counts = [
        "COMPLETE"=>0,
        "SKIP"=>0,
        "ERROR"=>0,
    ];

    public function __construct($dir){

        // get images from dir
        $arImages = self::GetDirContents($dir);

        foreach($arImages as $key => $arItem){

            // skip
            if(file_exists($arItem["IMAGE_WEBP"]) && $this->params["WEBP_SKIP_EXIST"]){

                $this->counts["SKIP"]++;
                continue;
            }

            // imagick
            $img = new Imagick($arItem["IMAGE"]);
            $img->setImageCompressionQuality($this->params["WEBP_QUALITY"]);
            $img->writeImage($arItem["IMAGE_WEBP"]);

            // wrong webp
            if(filesize($arItem["IMAGE_WEBP"]) < 100){

                unlink($arItem["IMAGE_WEBP"]);
                $this->counts["ERROR"]++;
                continue;

            }

            $this->counts["COMPLETE"]++;

            if($this->params["LIMIT"] && $this->counts["COMPLETE"] >= $this->params["LIMIT"]){
                break;
            }

        }

    }

    public function GetCounts(){

        return $this->counts;

    }

    private function GetDirContents($dir = '', &$results = []){

        if(empty($dir)){ return []; }

        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {

                $pathLower = mb_strtolower($path);

                if(mb_substr($pathLower, -4) == '.jpg'){
                    $results[] = [
                        "IMAGE"=>$path,
                        "IMAGE_WEBP"=>mb_substr($path, 0, -3) . 'webp',
                        "TYPE"=>"jpg",
                    ];
                }

                if(mb_substr($pathLower, -5) == '.jpeg'){
                    $results[] = [
                        "IMAGE"=>$path,
                        "IMAGE_WEBP"=>mb_substr($path, 0, -4) . 'webp',
                        "TYPE"=>"jpeg",
                    ];
                }

                if(mb_substr($pathLower, -4) == '.png'){
                    $results[] = [
                        "IMAGE"=>$path,
                        "IMAGE_WEBP"=>mb_substr($path, 0, -3) . 'webp',
                        "TYPE"=>"png",
                    ];
                }

            } else if ($value != '.' && $value != '..') {
                self::GetDirContents($path, $results);
            }
        }

        return $results;

    }

}
