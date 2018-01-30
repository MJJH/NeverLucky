<?php namespace engine\controller;

interface IController {
    
    function render();
    function onPost($postdata);
    
    function url_for($resource);
}