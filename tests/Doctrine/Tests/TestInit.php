<?php
/*
 * This file bootstraps the test environment.
 */
namespace Doctrine\Tests;

error_reporting(E_ALL | E_STRICT);
defined('VENDOR_DIR')?:define('VENDOR_DIR',__DIR__ . '/../../../vendor');
require_once VENDOR_DIR.'/doctrine/common/lib/Doctrine/Common/ClassLoader.php';

if (isset($GLOBALS['DOCTRINE_COMMON_PATH'])) {
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', $GLOBALS['DOCTRINE_COMMON_PATH']);
} else {
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common\Collections', VENDOR_DIR . '/doctrine/collections/lib');
    $classLoader->register();
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common\Annotations', VENDOR_DIR . '/doctrine/annotations/lib');
    $classLoader->register();
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common\Cache', VENDOR_DIR . '/doctrine/cache/lib');
    $classLoader->register();
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common\Inflector', VENDOR_DIR . '/doctrine/inflector/lib');
    $classLoader->register();
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common\Lexer', VENDOR_DIR . '/doctrine/lexer/lib');
    $classLoader->register();
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', VENDOR_DIR . '/doctrine/common/lib');
}
$classLoader->register();

if (isset($GLOBALS['DOCTRINE_DBAL_PATH'])) {
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', $GLOBALS['DOCTRINE_DBAL_PATH']);
} else {
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', VENDOR_DIR.'/doctrine/dbal/lib');
}
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ORM', __DIR__ . '/../../../lib');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Tests', __DIR__ . '/../../');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Symfony\Component\Yaml', VENDOR_DIR.'/symfony/yaml');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Symfony\Component\Console', VENDOR_DIR.'/symfony/console');
$classLoader->register();

if (!file_exists(__DIR__."/Proxies")) {
    if (!mkdir(__DIR__."/Proxies")) {
        throw new \Exception("Could not create " . __DIR__."/Proxies Folder.");
    }
}
if (!file_exists(__DIR__."/ORM/Proxy/generated")) {
    if (!mkdir(__DIR__."/ORM/Proxy/generated")) {
        throw new \Exception("Could not create " . __DIR__."/ORM/Proxy/generated Folder.");
    }
}
