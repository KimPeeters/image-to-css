<?php

class ImgToCss
{
    private $files = array();
    private $base64files = array();
    private $fileMap = array();

    public function addDirectory($dir)
    {
        if ( is_dir($dir) ) {
            $list = scandir($dir);
            foreach($list as $item) {
                $file = rtrim($dir,'/').'/'.$item;
                if ( is_file($file) ) {
                    $this->addFile($file,true);
                }
            }
        }
    }

    public function addFile($file, $ignoreErrors = false)
    {
        if ( is_file($file) ) {
            if ( $this->getFileType($file) ) {
                $this->files[] = $file;
            } elseif ( !$ignoreErrors ) {
                throw new Exception('File ('.$file.') not supported.');
            }
        } elseif ( !$ignoreErrors ) {
            throw new Exception('File ('.$file.') does not exist.');
        }
    }

    public function getMap()
    {
        return $this->fileMap;
    }

    public function run()
    {
        foreach($this->files as $file) {
            $content = file_get_contents($file);
            $encContent = base64_encode($content);

            $className = $this->getUniqueClassName($file);

            $type = $this->getFileType($file);

            $sizes = $this->getImageSize($file);

            $this->base64files[$className] = array('type' => $type, 'size' => $sizes, 'content' => $encContent);
        }

        return $this->generateOutput();
    }

    private function getImageSize($file)
    {
        $sizes =  getimagesize($file);
        return array('w'=>$sizes[0],'h'=>$sizes[1]);
    }

    private function getFileType($file)
    {
        switch ( @exif_imagetype($file) )
        {
            case IMAGETYPE_GIF: return 'gif'; break;
            case IMAGETYPE_PNG: return 'png'; break;
            case IMAGETYPE_JPEG: return 'jpeg'; break;
        }
        return '';
    }

    private function getClassName($file)
    {
        $basename = basename($file);
        $exBase = explode('.',$basename);
        $baseWOextension = implode('',array_slice($exBase,0,max(1,count($exBase)-1)));
        $class = preg_replace("#([^a-z0-9\-])#i",'',$baseWOextension);

        return $class;
    }

    private function getUniqueClassName($file)
    {
        $orgClass = $this->getClassName($file);
        $class = $orgClass;
        $i=1;
        while ( array_key_exists($class,$this->fileMap) ) {
            $class = $orgClass.'-'.$i;
            $i++;
        }
        $this->fileMap[$file] = $class;
        return $class;
    }

    private function generateOutput()
    {
        $content = '/* auto generated file */'.PHP_EOL;
        foreach($this->base64files as $class => $encContent) {

            $content .= '.img-'.$class.' { display:block;width:'.$encContent['size']['w'].'px;height:'.$encContent['size']['h'].'px; background-image: url(data:image/'.$encContent['type'].';base64,'.$encContent['content'].'); }'.PHP_EOL;
        }
        return $content;
    }
}
