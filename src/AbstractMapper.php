<?php

namespace DealNews\DataMapper;

use \DealNews\DataMapper\Interfaces\Mapper;
use \DealNews\Constraints\Constraint;
use \DealNews\Constraints\ConstraintException as UpstreamConstraintException;

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
    public const MAPPED_CLASS = '';

    /**
     * Defines the properties that are mapped and any
     * additional information needed to map them.
     */
    protected const MAPPING = [];

    /**
     * Defines the property in the object that represents
     * a unique value for the object.
     */
    protected const PRIMARY_KEY = '';

    /**
     * Constraint Object
     */
    protected ?Constraint $constraint;

    public function __construct(Constraint $constraint = null) {
        $this->constraint = $constraint;
    }

    /**
     * Returns the name of the primary key property for
     * the object being mapped
     *
     * @return int|string
     */
    public function getPrimaryKey() {
        return static::PRIMARY_KEY;
    }

    /**
     * Returns the name of the class the mapper saves/loads
     *
     * @return string
     */
    public static function getMappedClass(): string {
        return static::MAPPED_CLASS;
    }

    /**
     * Applies the loaded data to the object. This can be overridden by a
     * child class when more complex work needs to be done.
     *
     * @param array $data Array of data loaded from the database
     *
     * @suppress PhanTypeExpectedObjectOrClassName, PhanEmptyFQSENInClasslike
     */
    protected function setData(array $data): object {
        $class  = $this::MAPPED_CLASS;
        $object = new $class();
        foreach ($this::MAPPING as $property => $mapping) {
            $this->setValue($object, $property, $data, $mapping);
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
    protected function getData(object $object): array {
        $data = [];
        foreach ($this::MAPPING as $property => $mapping) {
            // remove read only values from the data that is to be
            // inserted/updated in the database
            if (empty($mapping['read_only'])) {
                $key        = $mapping['rename'] ?? $property;
                $data[$key] = $this->getValue($object, $property, $mapping);
            }
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
    protected function setValue(object $object, string $property, array $data, array $mapping) {
        if (!empty($mapping['rename']) && array_key_exists($mapping['rename'], $data)) {
            $data[$property] = $data[$mapping['rename']];
        }

        if (array_key_exists($property, $data)) {
            $value = $data[$property];

            if (!empty($mapping['encoding'])) {
                switch ($mapping['encoding']) {
                    case 'json':
                        $assoc   = $mapping['json_assoc'] ?? null;
                        $depth   = $mapping['json_depth'] ?? 512;
                        $options = $mapping['json_options'] ?? 0;
                        $value   = json_decode($value, $assoc, $depth, $options);
                        break;
                    case 'yaml':
                        $value = yaml_parse($value);
                        break;
                    case 'serialize':
                        $options = $mapping['unserialize_options'] ?? [];
                        $value   = unserialize($value, $options);
                        break;
                    default:
                        throw new \LogicException("Unsupported encoding {$mapping['encoding']} for property $property");
                }
            }

            if (!empty($mapping['class'])) {
                if (
                    is_a($mapping['class'], "\DateTime", true) ||
                    is_a($mapping['class'], "\DateTimeImmutable", true) ||
                    is_subclass_of($mapping['class'], "\DateTime", true) ||
                    is_subclass_of($mapping['class'], "\DateTimeImmutable", true)
                ) {
                    $timezone = $mapping['timezone'] ?? null;
                    $format   = $mapping['format'] ?? 'Y-m-d H:i:s';
                    $value    = $mapping['class']::createFromFormat($format, $value, $timezone);
                } else {

                    if (!empty($mapping['one_to_many'])) {
                        $new_value = [];
                        foreach ($value as $v) {
                            $new_value[] = $this->createObject($mapping['class'], $v);
                        }
                        $value = $new_value;
                    } else {
                        $value = $this->createObject($mapping['class'], $value);
                    }
                }
            }

            if (gettype($object->$property) === 'boolean') {
                if (isset($mapping['bool_values'])) {
                    $vals  = array_flip($mapping['bool_values']);
                    $value = $vals[$value] === 'true';
                } elseif (gettype($value) !== 'boolean') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                }
                if ($value === null) {
                    $value = false;
                }
            }

            if (
                is_array($value) &&
                is_object($object->$property) &&
                $object->$property instanceof \ArrayObject
            ) {
                $object->$property->exchangeArray($value);
                $value = $object->$property;
            }

            $object->$property = $value;
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
    protected function getValue(object $object, string $property, array $mapping) {
        $value = $object->$property;

        if (!empty($mapping['encoding'])) {
            switch ($mapping['encoding']) {
                case 'json':
                    $value = json_encode($value);
                    break;
                case 'yaml':
                    $value = yaml_emit($value);
                    break;
                case 'serialize':
                    $value = serialize($value);
                    break;
                default:
                    throw new \LogicException("Unsupported encoding {$mapping['encoding']} for property $property");
            }
        }

        if (!empty($mapping['class'])) {
            if (
                is_a($mapping['class'], "\DateTime", true) ||
                is_a($mapping['class'], "\DateTimeImmutable", true) ||
                is_subclass_of($mapping['class'], "\DateTime", true) ||
                is_subclass_of($mapping['class'], "\DateTimeImmutable", true)
            ) {
                $format = $mapping['format'] ?? 'Y-m-d H:i:s';
                $value  = $object->$property->format($format);
            } else {

                if (!empty($mapping['one_to_many'])) {
                    $new_value = [];
                    foreach ($value as $v) {
                        $new_value[] = (array)$v;
                    }
                    $value = $new_value;
                } else {
                    $value = (array)$value;
                }
            }
        }

        if (isset($mapping['constraint'])) {
            $constraint = $this->constraint ?? new Constraint();

            $property_constraint = array_merge(
                [
                    'type' => gettype($value),
                ],
                $mapping['constraint']
            );

            try {
                $value = $constraint->check($value, $property_constraint);
            } catch (UpstreamConstraintException $e) {
                throw new ConstraintException(
                    $property,
                    $value,
                    $e->getExpected(),
                    $e->getExample(),
                    $e->getCode(),
                    $e->getPrevious()
                );
            }
        }

        if (isset($mapping['bool_values'])) {
            if ($value === true) {
                $value = $mapping['bool_values']['true'];
            } else {
                $value = $mapping['bool_values']['false'];
            }
        }

        return $value;
    }

    protected function createObject(string $class, array $data): object {
        $obj = new $class();
        foreach ($data as $prop => $value) {
            if (property_exists($obj, $prop)) {
                $obj->$prop = $value;
            }
        }

        return $obj;
    }
}
