<?php

namespace DealNews\DataMapper\Tests\TestClasses;

/**
 * Test Course Class
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     DataMapper
 */
class Course {

    /**
     * The unique id
     *
     * @var integer
     */
    public int $course_id = 0;

    /**
     * A short name
     *
     * @var string
     */
    public string $name = '';

    public string $create_datetime = '';

    public bool $active = false;

    public \ArrayObject $students;

    public Teacher $teacher;

    public function __construct() {
        $this->students = new \ArrayObject();
        $this->teacher  = new Teacher();
    }
}
