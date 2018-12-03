<?php
use Leafo\ScssPhp\Compiler;
use MatthiasMullie\Minify;

class LocalCompiler
{

    public $scss;

    protected $compiled;

    protected $minifierCss;

    protected $minifierJs;

    protected $files;

    protected $locationCss = __DIR__.'/public/css/final/';

    protected $locationJs = __DIR__.'/public/js/final/';

    function __construct()
    {
        $this->scss = new Compiler();
    }

    function startCompile()
    {
        $this->scss->setImportPaths(__DIR__."/public/css/local/sass/");
        if($this->compiled = $this->scss->compile('@import "import.scss"')){
            return true;
        }
        return false;
    }

    function saveCompiled()
    {
        file_put_contents(__DIR__ . '/public/css/local/style_compile.css', $this->compiled);
    }


    function getAllCssFiles()
    {

        $dir = __DIR__.'/public/css/vendor/';
        $this->recurseDir($dir,"css");

        $dir = __DIR__.'/public/css/local/';
        $this->recurseDir($dir,"css");

    }

    function getAllJsFiles()
    {
        $dir = __DIR__.'/public/js/vendor/';
        $this->recurseDir($dir,"js");

        $dir = __DIR__.'/public/js/local/';
        $this->recurseDir($dir,"js");

    }
    function recurseDir($dir,$type = "css") {
        if(is_dir($dir)) {
            if($dh = opendir($dir)){
                while($file = readdir($dh)){
                    if($file != '.' && $file != '..'){
                        if(is_dir($dir . $file)){
                            // since it is a directory we recurse it.
                            $this->recurseDir($dir . $file . '/',$type);
                        }else{
                            if(pathinfo($dir . $file,PATHINFO_EXTENSION) == $type){
                                $this->files[$type][] = $dir . $file;  
                            } 
                         }
                    }
                 }
            }
             closedir($dh);         
             }
    }

    function startMinifyCss()
    {
        $count = 1;
        foreach($this->files["css"] as $cssFile){
            if($count == 1){
                $this->minifierCss = new Minify\CSS($cssFile);
            }else{
                $this->minifierCss->add($cssFile);
            }
            $count++;

        }
    }


    function startMinifyJs()
    {
        $count = 1;
        foreach($this->files["js"] as $jsFile){
            if($count == 1){
                $this->minifierJs = new Minify\JS($jsFile);
            }else{
                $this->minifierJs->add($jsFile);
            }
            $count++;

        }
    }

    function saveMinifiedCss($type = "css")
    {
        $location = $this->locationCss;
        $minifiers = "minifier".ucfirst($type);

        $contents = $this->$minifiers->minify();
        $hash = md5($contents);
        $minifiedPath = $location.$hash.'.css';
        if(! file_exists($minifiedPath)){
            array_map('unlink', array_filter((array) glob($location."*")));
            $this->$minifiers->minify($minifiedPath);
        }

    }



    function saveMinifiedJs($type = "js")
    {
        $location = $this->locationJs;
        $minifiers = "minifier".ucfirst($type);

        $contents = $this->$minifiers->minify();
        $hash = md5($contents);
        $minifiedPath = $location.$hash.'.js';
        if(! file_exists($minifiedPath)){
            array_map('unlink', array_filter((array) glob($location."*")));
            $this->$minifiers->minify($minifiedPath);
        }

    }
}
