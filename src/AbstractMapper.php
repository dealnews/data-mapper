<?php

namespace DealNews\DataMapper;

use \DealNews\DataMapper\Interfaces\Mapper;

/**
 * Abstract Mapper class providing basic reusable functions
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     DataMapper
 */
abstract class AbstractMapper implements Mapper {

    /**
     * Name of the class the mapper is mapping
     */
    public const MAPPED_CLASS = "";

    /**
     * Defines the properties that are mapped and any
     * additional information needed to map them.
     */
    protected const MAPPING = [];

    /**
     * Defines the property in the object that represents
     * a unique value for the object.
     */
    protected const PRIMARY_KEY = "";

    /**
     * Returns the name of the primary key property for
     * the object being mapped
     *
     * @return int|string
     */
    public function get_primary_key() {
        return static::PRIMARY_KEY;
    }

    /**
     * Returns the name of the class the mapper saves/loads
     *
     * @return string
     */
    public static function get_mapped_class() {
        return static::MAPPED_CLASS;
    }

    /**
     * Applies the loaded data to the object. This can be overridden by a
     * child class when more complex work needs to be done.
     *
     * @param array $data Array of data loaded from the database
     *
     * @suppress PhanTypeExpectedObjectOrClassName
     */
    protected function set_data(array $data) {
        $class = $this::MAPPED_CLASS;
        $object = new $class();
        foreach ($this::MAPPING as $property => $mapping) {
            $this->set_value($object, $property, $data, $mapping);
        }
        return $object;
    }

    /**
     * Builds a data array for insertion into the database using the object
     * properties. This can be overridden by a child class when more complex
     * work needs to be done.
     *
     * @param object $object
     *
     * @return array
     */
    protected function get_data($object) {
        $data = array();
        foreach ($this::MAPPING as $property => $mapping) {
            $data[$property] = $this->get_value($object, $property, $mapping);
        }
        return $data;
    }

    /**
     * Sets a value on the object. This can be overridden by a child
     * class when more complex work needs to be done to set a value.
     *
     * @param object $object   Object on which to so set the value
     * @param string $property Property name
     * @param array  $data     Array containing data from storage
     * @param array  $mapping  Mapping array for the property
     *
     * @suppress PhanUnusedProtectedMethodParameter
     */
    protected function set_value($object, $property, $data, $mapping) {
        if (array_key_exists($property, $data)) {
            $object->$property = $data[$property];
        }
    }

    /**
     * Get a value from an object. This can be overridden by a child
     * class when more complex work needs to be done to get a value.
     *
     * @param object $object   Object from which to so get the value
     * @param string $property Property name
     * @param array  $mapping  Mapping array for the property
     *
     * @suppress PhanUnusedProtectedMethodParameter
     */
    protected function get_value($object, $property, $mapping) {
        return $object->$property;
    }
}
