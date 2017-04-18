<?php

namespace Ihsan\Client\Platform\Cache;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class CacheHandler
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir = null)
    {
        if ($cacheDir) {
            $this->cacheDir = $cacheDir;
        } else {
            $this->cacheDir = sys_get_temp_dir();
        }
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param string           $content
     *
     * @throws \RuntimeException
     */
    public function write(\ReflectionClass $reflectionClass, $content)
    {
        $cacheFile = $this->getCacheFile($reflectionClass);

        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);

            @chmod($cacheDir, 0777 & ~umask());
        }

        $tmpFile = tempnam(dirname($cacheFile), basename($cacheFile));
        if (false !== @file_put_contents($tmpFile, sprintf('<?php return unserialize(\'%s\');', serialize($content))) && @rename($tmpFile, $cacheFile)) {
            @chmod($cacheFile, 0666 & ~umask());

            return;
        }

        throw new \RuntimeException($cacheFile);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool
     */
    public function has(\ReflectionClass $reflectionClass)
    {
        if (file_exists($this->getCacheFile($reflectionClass))) {
            return true;
        }

        return false;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return string
     */
    public function fetch(\ReflectionClass $reflectionClass)
    {
        return require $this->getCacheFile($reflectionClass);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return string
     */
    private function getCacheFile(\ReflectionClass $reflectionClass)
    {
        return sprintf('%s/%s.php.cache', $this->cacheDir, str_replace('\\', '_', $reflectionClass->getName()));
    }
}
