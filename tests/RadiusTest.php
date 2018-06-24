<?php


namespace Tests;


use PHPUnit\Framework\TestCase;

class RadiusTest extends TestCase {


    public function testMd5(){
        echo md5("ha");
        echo md5("ha\x0");
    }
}