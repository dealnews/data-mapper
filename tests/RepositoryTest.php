<?php

namespace DealNews\DataMapper\Tests;

use \DealNews\DataMapper\Repository;
use \DealNews\DataMapper\Tests\TestClasses\Course;
use \DealNews\DataMapper\Tests\TestClasses\CourseChild;
use \DealNews\DataMapper\Tests\TestClasses\Mapper\CourseMapper;

class RepositoryTest extends \PHPUnit\Framework\TestCase {
    public function testSaving() {
        $this->save();
    }

    public function testSaveChild() {
        $repo = new Repository();
        $repo->addMapper('CourseChild', new CourseMapper);

        $course       = new CourseChild();
        $course->name = "Child Test";

        $course = $repo->save('CourseChild', $course);

        $this->assertEquals(
            "Child Test",
            $course->name
        );
    }

    public function testLoading() {
        $id = $this->save();

        $repo = new Repository(
            [
                'Course' => new CourseMapper,
            ]
        );

        $courses = $repo->get('Course', [$id]);

        $this->assertNotEmpty(
            $courses
        );

        $this->assertEquals(
            $id,
            current($courses)->course_id
        );
    }

    public function testDelete() {
        $repo = new Repository(
            [
                'Course' => new CourseMapper,
            ]
        );

        $id = $this->save('TestDelete');

        $courses = $repo->get('Course', [$id]);

        $this->assertNotEmpty(
            $courses
        );

        $result = $repo->delete('Course', $id);

        $this->assertTrue(
            $result
        );

        $courses = $repo->get('Course', [$id]);

        $this->assertEmpty(
            $courses
        );
    }

    public function testNew() {
        $repo = new Repository(
            [
                'Course' => new CourseMapper,
            ]
        );

        $course = $repo->new('Course');

        $this->assertTrue(
            $course instanceof Course
        );
    }

    public function testFind() {
        $id = $this->save('TestFind');

        $repo = new Repository(
            [
                'Course' => new CourseMapper,
            ]
        );

        $courses = $repo->find('Course', ['name' => 'TestFind']);

        $this->assertNotEmpty(
            $courses
        );

        $this->assertEquals(
            $id,
            current($courses)->course_id
        );
    }

    public function testBadNew() {
        $this->expectException("\LogicException");
        $this->expectExceptionCode(1);

        $repo = new Repository();
        $obj  = $repo->new('Foo');
    }

    public function testBadDelete() {
        $this->expectException("\LogicException");
        $this->expectExceptionCode(2);
        $repo = new Repository();
        $obj  = $repo->delete('Foo', 1);
    }

    public function testBadFind() {
        $this->expectException("\LogicException");
        $this->expectExceptionCode(3);
        $repo = new Repository();
        $obj  = $repo->find('Foo', []);
    }

    public function testBadClass() {
        $this->expectException("\LogicException");
        $this->expectExceptionCode(4);
        $repo = new Repository();
        $obj  = $repo->addMapper('bad', new BadMapper());
    }

    protected function save($name = 'Test') {
        $repo = new Repository();
        $repo->addMapper('Course', new CourseMapper);

        $course       = new Course();
        $course->name = $name;

        $course = $repo->save('Course', $course);

        $this->assertEquals(
            $name,
            $course->name
        );

        return $course->course_id;
    }
}

class BadMapper extends \DealNews\DataMapper\AbstractMapper {
    public function load($id) {
    }

    public function loadMulti(array $ids) {
    }

    public function find(array $filter) {
    }

    public function save($object) {
    }

    public function delete($id) {
    }
}
