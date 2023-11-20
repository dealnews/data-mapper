<?php

namespace DealNews\DataMapper;

/**
 * Exception class for constraint exceptions
 *
 * This is mostly a wrapper for \DealNews\Constraints\ConstraintException.
 * It adds the property name to allow for more specific error messages to
 * be sent to client applications.
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     DataMapper
 */
class ConstraintException extends \DealNews\Constraints\ConstraintException {

    /**
     * The object property which generated the exception
     */
    protected string $property;

    /**
     * Constructs a new instance.
     *
     * @param string          $property The property which generated the exception
     * @param mixed           $value    The invalid value
     * @param string          $expected A human friendly explination of the expected value
     * @param string          $example  A human friendly example of valid values
     * @param integer         $code     A unique code for this thrown exception
     * @param \Throwable|null $previous A previously thrown exception which was caught
     */
    public function __construct(string $property, $value, string $expected, string $example, int $code = 0, \Throwable $previous = null) {
        parent::__construct($value, $expected, $example, $code, $previous);
        $this->property = $property;
    }

    /**
     * Gets the property.
     *
     * @return     string  The property.
     */
    public function getProperty(): string {
        return $this->property;
    }
}
