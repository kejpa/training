<?php

declare (strict_types=1);

namespace kejpa\training\Framework;

use Noodlehaus\Config;
use Noodlehaus\ConfigInterface;
use Noodlehaus\Parser\Json;
/**
 * Description of configuration
 *
 * @author kjell
 */
final class Configuration implements ConfigInterface{

    private $file;
    private $config;

    public function __construct(string $file) {
        $this->file = $file;
        $this->config = Config::load($file);
    }

    public function get($setting, $default = NULL) {
        return $this->config->get($setting, $default);
    }

    public function all(): array {
        return $this->config->all();
    }

    public function has($key): boolean {
        return $this->config->has($key);
    }

    public function set($key, $value): void {
        $this->config->set($key, $value);
        $this->config->toFile($this->file);
    }

}
