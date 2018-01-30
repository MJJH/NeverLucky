<?php namespace engine\lessphp;

if (!function_exists('glob_recursive'))
{
    // Does not support flag GLOB_BRACE        
   function glob_recursive($pattern, $flags = 0)
   {
     $files = glob($pattern, $flags);
     foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
     {
       $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
     }
     return $files;
   }
}

function createFile($pathToFile) {
    $fileName = basename($pathToFile);
    $folders = explode('/', str_replace('/' . $fileName, '', $pathToFile));
    
    $currentFolder = '';
    foreach ($folders as $folder) {
        $currentFolder .= $folder . DIRECTORY_SEPARATOR;
        if (!file_exists($currentFolder)) {
            mkdir($currentFolder, 0755);
        }
    }
}


function compileFolder($input_folder, $output_folder) {
    if (!file_exists($input_folder)) {
        throw new \Exception("Folder \"{$input_folder}\" not found");
    }
    
    if (!file_exists($output_folder)) {
        mkdir($output_folder, 0777, true);
    }
    
    $files = glob_recursive($input_folder."/*.less", GLOB_BRACE);
    $less = new \lessc;
    define('less', '.less');
    define('css', '.css');
    
    foreach ($files as $file) {
        $file_name = str_replace($input_folder, "", $file);
        $file_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_name);
        
        if (substr($file_name[0], 0, 1) === "_")
            continue;
        
        $in = $input_folder.$file_name.less;
        $out = $output_folder.$file_name.css;
        
        if (!file_exists($out))
            createFile($out);
    
        if ($less->checkedCompile($in, $out)) {
            $less->compileFile($in, $out);
        }
    }
}