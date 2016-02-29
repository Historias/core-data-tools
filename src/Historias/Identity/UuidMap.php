<?php
namespace Historias\Importer\Identity;

use Historias\Importer\Util\JsonUtil;
use Ramsey\Uuid\Uuid;

class UuidMap
{
    private $idMap = [];

    private $path;

    public function __construct($path)
    {
        $this->path = $path;
        if (file_exists($path)) {
            $this->idMap = JsonUtil::decode(file_get_contents($path));
        }
    }

    public function get($key) : Uuid
    {
        if (!isset($this->idMap[$key])) {
            $this->idMap[$key] = Uuid::uuid4();
        }

        return Uuid::fromString($this->idMap[$key]);
    }

    public function flush()
    {
        ksort($this->idMap);
        file_put_contents($this->path, JsonUtil::encode($this->idMap));
    }

    public function __destruct()
    {
        $this->flush();
    }
}
