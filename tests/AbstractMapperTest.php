<?php

namespace DealNews\DataMapper\Tests;

use \DealNews\DataMapper\Tests\TestClasses\Course;
use \DealNews\DataMapper\Tests\TestClasses\Mapper\CourseMapper;

class AbstractMapperTest extends \PHPUnit\Framework\TestCase {
    public function testSetData() {
        $mapper = new CourseMapper();
        $course = $mapper->testSetData([
            'course_id'   => 1,
            'name'        => 'Test Course',
            'create_date' => '2020-01-01',
        ]);

        $this->assertEquals(1, $course->course_id);
        $this->assertEquals('Test Course', $course->name);
        $this->assertEquals('2020-01-01', $course->create_date);
    }

    public function testGetData() {
        $course              = new Course();
        $course->course_id   = 2;
        $course->name        = 'Test Course 2';
        $course->create_date = '2020-01-01';

        $mapper = new CourseMapper();
        $data   = $mapper->testGetData($course);

        $this->assertEquals(
            [
                'course_id' => 2,
                'name'      => 'Test Course 2',
            ],
            $data
        );
    }

    /**
     * @dataProvider constraintData
     */
    public function testConstraints($input, $expected_message, $property) {
        $mapper = new CourseMapper();
        $course = $mapper->testSetData($input);
        try {
            $mapper->testGetData($course);
        } catch (\DealNews\DataMapper\ConstraintException $e) {
            $this->assertNotEmpty(preg_match($expected_message, $e->getMessage()));
            $this->assertEquals($property, $e->getProperty());
        }
    }

    public function constraintData() {
        return [

            'Empty Name' => [
                [
                    'name' => '',
                ],
                '/Expected: minimum length of 1\.$/',
                'name',
            ],

            'Long Name' => [
                [
                    'name' => str_repeat('x', 101),
                ],
                '/Expected: maximum length of 100\.$/',
                'name',
            ],

        ];
    }
}
