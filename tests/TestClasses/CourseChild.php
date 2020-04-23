<?php

namespace DealNews\DataMapper\Tests\TestClasses;

/**
 * Test Child of Course Class
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     DataMapper
 */
class CourseChild extends Course {

    /**
     * The unique id
     *
     * @var integer
     */
    public $course_id = 0;

    /**
     * A short name
     *
     * @var string
     */
    public $name = '';
}
