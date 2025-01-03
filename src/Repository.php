<?php

namespace DealNews\DataMapper;

use \DealNews\DataMapper\Interfaces\Mapper;

/**
 * DataMapper Repository
 *
 * Serves as a base repository for DataMappers
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     DataMapper
 *
 * @phan-suppress PhanUnreferencedClass
 */
class Repository extends \DealNews\Repository\Repository {

    /**
     * Keeps the list of mappers
     *
     * @var array
     */
    protected array $mappers = [];

    /**
     * Keeps the list of classes
     *
     * @var array
     */
    protected array $classes = [];

    /**
     * Creates the repository
     * @param array $mappers Array of mappers to add when creating the object
     */
    public function __construct(array $mappers = []) {
        if (!empty($mappers)) {
            foreach ($mappers as $name => $mapper) {
                $this->addMapper($name, $mapper);
            }
        }
    }

    /**
     * Returns a new object of type $name
     *
     * @param string         $name   Mapped Object name
     */
    public function new(string $name): object {
        $class_name = $this->findClass($name);

        return new $class_name();
    }

    /**
     * Deletes an object from the repository and underlying storage via the mapper
     *
     * @param string         $name   Mapped Object name
     * @param string|int     $id     Unique identifier
     *
     * @return boolean
     */
    public function delete(string $name, $id): bool {
        $class_name = $this->findClass($name);
        $mapper     = $this->mappers[trim($class_name, '\\')];

        if (isset($this->storage[$name]) && array_key_exists($id, $this->storage[$name])) {
            unset($this->storage[$name][$id]);
        }

        return $mapper->delete($id);
    }

    /**
     * Returns objects matching the filters. This method does not check
     * the Repository storage for data. The data is returned from the Mapper.
     * The data is stored in the Repository storage however after it is
     * retrieved.
     *
     * @param  string    $name    Mapped Object name
     * @param  array     $filters Array of filters. See \DealNews\DB\AbstractMapper::find()
     * @param  int|null  $limit   Number of matches to return
     * @param  int|null  $start   Start position
     * @param  string    $order   The order of returned matches
     *
     * @return array
     */
    public function find(string $name, array $filters, ?int $limit = null, ?int $start = null, string $order = ''): array {
        $mapper = $this->getMapper($name);
        $data   = $mapper->find($filters, $limit, $start, $order);
        if (!empty($data)) {
            $this->setMulti($name, $data);
        }

        if (empty($data)) {
            $data = [];
        }

        return $data;
    }

    /**
     * Adds a mapper to the Repository
     *
     * @param string         $name   Mapped Object name
     * @param AbstractMapper $mapper Mapper object
     */
    public function addMapper(string $name, Mapper $mapper): void {
        $class_name = trim($mapper::getMappedClass(), '\\');

        if (!class_exists($class_name)) {
            throw new \LogicException("Class `$class_name` not found for `$name`", 4);
        }

        $this->mappers[$class_name] = $mapper;
        $this->classes[$name]       = $class_name;
        $this->register($name, [$mapper, 'loadMulti'], [$this, 'mapperSave']);
    }

    /**
     * Gets the mapper for a mapped object name
     *
     * @param string   $name   Mapped Object name
     *
     * @return Mapper
     */
    public function getMapper(string $name): Mapper {
        $class_name = $this->findClass($name);

        return $this->mappers[trim($class_name, '\\')];
    }

    /**
     * Internal function to handle mapper saves
     *
     * @param  object $object The object to save
     * @return array|bool
     */
    protected function mapperSave($object): bool|array {
        $return = false;
        $class  = trim(get_class($object), '\\');

        // See if a mapper exists that can map a
        // parent class of the object
        if (!isset($this->mappers[$class])) {
            $parents = class_parents($object);
            foreach ($parents as $parent) {
                $parent = trim($parent, '\\');
                if (isset($this->mappers[$parent])) {
                    $class = $parent;
                    break;
                }
            }
        }

        if (isset($this->mappers[$class])) {
            $object = $this->mappers[$class]->save($object);
            if ($object) {
                $key    = $this->mappers[$class]->getPrimaryKey();
                $return = [
                    $object->$key => $object,
                ];
            }
        }

        return $return;
    }

    /**
     * Finds the class name for the registered name
     *
     * @param string   $name   Mapped Object name
     *
     * @return string
     */
    protected function findClass(string $name): string {
        if (!isset($this->classes[$name])) {
            throw new \LogicException("There is no class registered for `$name`", 1);
        }
        $class_name = $this->classes[$name];

        return $class_name;
    }
}
