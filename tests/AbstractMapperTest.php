<?php

namespace DealNews\DataMapper\Tests;

use \DealNews\DataMapper\Tests\TestClasses\Course;
use \DealNews\DataMapper\Tests\TestClasses\Mapper\CourseMapper;
use \DealNews\DataMapper\Tests\TestClasses\Student;
use \DealNews\DataMapper\Tests\TestClasses\Teacher;

class AbstractMapperTest extends \PHPUnit\Framework\TestCase {

    public function testSetData() {
        $mapper = new CourseMapper();

        $course = $mapper->testSetData([
            'course_id'   => 1,
            'name'        => 'Test Course',
            'active'      => 'foo',
            'teacher'     => [
                'teacher_id'      => 1,
                'name'            => 'Teacher 1',
                'create_datetime' => '2020-01-03',
            ],
            'students'    => [
                [
                    'student_id'      => 2,
                    'name'            => 'Student 2',
                    'create_datetime' => '2020-01-02',
                ],
            ],
            'create_date' => '2020-01-01',
        ]);

        $this->assertEquals(1, $course->course_id);
        $this->assertEquals('Test Course', $course->name);
        $this->assertEquals('2020-01-01', $course->create_datetime);
        $this->assertTrue($course->students[0] instanceof Student);

        $this->assertEquals(
            '{"course_id":1,"name":"Test Course","create_datetime":"2020-01-01","active":false,"students":{"0":{"student_id":2,"name":"Student 2","create_datetime":"2020-01-02"}},"teacher":{"teacher_id":1,"name":"Teacher 1","create_datetime":"2020-01-03"}}',
            json_encode($course)
        );
    }

    public function testGetData() {
        $student                  = new Student();
        $student->student_id      = 2;
        $student->name            = 'Student 2';
        $student->create_datetime = '2020-01-02';

        $teacher                  = new Teacher();
        $teacher->teacher_id      = 1;
        $teacher->name            = 'Teacher 1';
        $teacher->create_datetime = '2020-01-03';

        $course                  = new Course();
        $course->course_id       = 2;
        $course->name            = 'Test Course 2';
        $course->create_datetime = '2020-01-01';
        $course->teacher         = $teacher;
        $course->students        = new \ArrayObject([$student]);

        $mapper = new CourseMapper();
        $data   = $mapper->testGetData($course);

        $this->assertEquals(
            [
                'course_id' => 2,
                'name'      => 'Test Course 2',
                'students'  => [
                    [
                        'student_id'      => 2,
                        'name'            => 'Student 2',
                        'create_datetime' => '2020-01-02',
                    ],
                ],
                'teacher'   => [
                    'teacher_id'      => 1,
                    'name'            => 'Teacher 1',
                    'create_datetime' => '2020-01-03',
                ],
                'active' => false,
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
                '/Expected: minimum length of 1\\.$/',
                'name',
            ],

            'Long Name' => [
                [
                    'name' => str_repeat('x', 101),
                ],
                '/Expected: maximum length of 100\\.$/',
                'name',
            ],

        ];
    }
}
