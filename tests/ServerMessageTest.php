<?php
/**
 *============================
 * author:Farmer
 * time:2018/8/11 12:08
 * blog:blog.icodef.com
 * function:测试服务器的信息获取
 *============================
 */


echo memory_get_usage();
echo "\n";
echo disk_total_space('.');
echo "\n";
//print_r(file_get_contents("/proc/cpuinfo"));
//echo "\n";
//print_r(file_get_contents("/proc/net/dev"));
//echo "\n";
//print_r(file_get_contents("/proc/meminfo"));
//echo "\n";
//$info=file_get_contents("/proc/net/dev");
//preg_match_all('/(\w+):(.*?)[\r\n]/',$info,$match);
//print_r($match);
//echo "\n";
print_r(file_get_contents("/proc/loadavg"));
echo "\n";
//$info = file_get_contents("/proc/stat");
//print_r($info);
//100 *(user + nice + system)/(user + nice + system + idle)
//user nice system idle irq
echo "\n";
