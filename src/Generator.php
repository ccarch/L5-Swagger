<?php

namespace L5Swagger;

use Config;
use File;

class Generator
{


    public static function generateDocs()
    {
        $appDir = config('l5-swagger.paths.annotations');
        $docDir = config('l5-swagger.paths.docs');
        if (! File::exists($docDir) || is_writable($docDir)) {
            // delete all existing documentation
            if (File::exists($docDir)) {
                File::deleteDirectory($docDir);
            }

            self::defineConstants(config('l5-swagger.constants') ?: []);

            File::makeDirectory($docDir);
            $excludeDirs = config('l5-swagger.paths.excludes');
            // $swagger = \Swagger\scan($appDir, ['exclude' => $excludeDirs]);

            if (version_compare(config('l5-swagger.swagger_version'), '3.0', '>=')) {
                $swagger = \OpenApi\scan($appDir, ['exclude' => $excludeDirs]);
            } else {
                $swagger = \Swagger\scan($appDir, ['exclude' => $excludeDirs]);
            }

            if (config('l5-swagger.paths.base') !== null) {
                $swagger->basePath = config('l5-swagger.paths.base');
            }

            $filename = $docDir.'/'.config('l5-swagger.paths.docs_json', 'api-docs.json');
            $swagger->saveAs($filename);

            $security = new SecurityDefinitions();
            $security->generate($filename);
        }
    }


    // public static function generateDocs()
    // {
    //     $appDir = config('l5-swagger.paths.annotations');
    //     $docDir = config('l5-swagger.paths.docs');
    //     if (!File::exists($docDir) || is_writable($docDir)) {
    //         // delete all existing documentation
    //         if (File::exists($docDir)) {
    //             File::deleteDirectory($docDir);
    //         }

    //         self::defineConstants(config('l5-swagger.constants') ?: []);

    //         File::makeDirectory($docDir);
    //         $excludeDirs = config('l5-swagger.paths.excludes');

    //         $files = File::allFiles($appDir);

    //         foreach ($files as $key => $file) {

    //             // dd($file);

    //             $real_path = $file->getRealPath();

    //             $file_name = $file->getFilename();

    //             self::generateDocsAll($real_path, $file_name, $excludeDirs);

    //             // $swagger = \OpenApi\scan($file->getRealPath());

    //             // dd($swagger, $file->getRealPath());
    //             # code...
    //         }

    //         // self::defineConstants(config('l5-swagger.constants') ?: []);

    //         // File::makeDirectory($docDir);
    //         // $excludeDirs = config('l5-swagger.paths.excludes');

    //         // if (version_compare(config('l5-swagger.swagger_version'), '3.0', '>=')) {
    //         //     $swagger = \OpenApi\scan($appDir, ['exclude' => $excludeDirs]);
    //         // } else {
    //         //     $swagger = \Swagger\scan($appDir, ['exclude' => $excludeDirs]);
    //         // }

    //         // if (config('l5-swagger.paths.base') !== null) {
    //         //     $swagger->basePath = config('l5-swagger.paths.base');
    //         // }

    //         // $filename = $docDir.'/'.config('l5-swagger.paths.docs_json', 'api-docs.json');
    //         // $swagger->saveAs($filename);

    //         // $security = new SecurityDefinitions();
    //         // $security->generate($filename);
    //     }
    // }

    public static function generateDocsAll($real_path, $file_name, $excludeDirs)
    {

        // \Log::info($file_name . '.json');
        if (version_compare(config('l5-swagger.swagger_version'), '3.0', '>=')) {
            $swagger = \OpenApi\scan($real_path, ['exclude' => $excludeDirs]);
        } else {
            $swagger = \Swagger\scan($real_path, ['exclude' => $excludeDirs]);
        }
        

        if (config('l5-swagger.paths.base') !== null) {
            $swagger->basePath = config('l5-swagger.paths.base');
        }

        // $filename = $docDir.'/'.config('l5-swagger.paths.docs_json', 'api-docs.json');
        $filename = config('l5-swagger.paths.docs') . '/' . $file_name . '.json';
        $swagger->saveAs($filename);

        $security = new SecurityDefinitions();
        $security->generate($filename);
    }

    protected static function defineConstants(array $constants)
    {
        if (!empty($constants)) {
            foreach ($constants as $key => $value) {
                defined($key) || define($key, $value);
            }
        }
    }
}
