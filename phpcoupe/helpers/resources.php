<?php
/**
 * Created by PhpStorm.
 * User: samuelcorradi
 * Date: 21/01/15
 * Time: 22:03
 */


if( ! function_exists('img') )
{

    function img($path)
    {

        $path = str_replace(array('/', '\\'), DS, $path);

        return WEBROOT . 'image' . DS . trim($path, DS);

    }

}

if( ! function_exists('font') )
{

    function font($path)
    {

        $path = str_replace(array('/', '\\'), DS, $path);

        return WEBROOT . 'font' . DS . trim($path, DS);

    }

}

if( ! function_exists('css') )
{

    function css($path)
    {

        $path = str_replace(array('/', '\\'), DS, $path);

        return WEBROOT . 'css' . DS . trim($path, DS);

    }

}