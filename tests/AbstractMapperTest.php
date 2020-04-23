<?php

namespace DealNews\DataMapper\Tests;

use \DealNews\DataMapper\Tests\TestClasses\Course;
use \DealNews\DataMapper\Tests\TestClasses\Mapper\CourseMapper;

class AbstractMapperTest extends \PHPUnit\Framework\TestCase {
    public function testSetData() {
        $mapper = new CourseMapper();
        $course = $mapper->testSetData([
            'course_id' => 1,
            'name'      => 'Test Course',
        ]);

        $this->assertEquals(1, $course->course_id);
        $this->assertEquals('Test Course', $course->name);
    }

    public function testGetData() {
        $course            = new Course();
        $course->course_id = 2;
        $course->name      = 'Test Course 2';

        $mapper = new CourseMapper();
        $data   = $mapper->testGetData($course);

        $this->assertEquals(2, $data['course_id']);
        $this->assertEquals('Test Course 2', $data['name']);
    }
}
