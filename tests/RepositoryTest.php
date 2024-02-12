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
        $repo = $this->getRepo();
        $repo->addMapper('CourseChild', new CourseMapper);

        $course       = new CourseChild();
        $course->name = 'Child Test';

        $course = $repo->save('CourseChild', $course);

        $this->assertEquals(
            'Child Test',
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

        $courses = $repo->getMulti('Course', [$id]);

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

        $courses = $repo->getMulti('Course', [$id]);

        $this->assertNotEmpty(
            $courses
        );

        $result = $repo->delete('Course', $id);

        $this->assertTrue(
            $result
        );

        $courses = $repo->getMulti('Course', [$id]);

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

    public function testGetMapper() {
        $repo = new Repository(
            [
                'Course' => new CourseMapper,
            ]
        );

        $mapper = $repo->getMapper('Course');

        $this->assertTrue($mapper instanceof CourseMapper);
    }

    public function testBadNew() {
        $this->expectException('\LogicException');
        $this->expectExceptionCode('1');
        $repo = $this->getRepo();
        $obj  = $repo->new('Foo');
    }

    public function testBadDelete() {
        $this->expectException('\LogicException');
        $this->expectExceptionCode('1');
        $repo = $this->getRepo();
        $obj  = $repo->delete('Foo', 1);
    }

    public function testBadFind() {
        $this->expectException('\LogicException');
        $this->expectExceptionCode('1');
        $repo = $this->getRepo();
        $obj  = $repo->find('Foo', []);
    }

    public function testBadClass() {
        $this->expectException('\LogicException');
        $this->expectExceptionCode('4');
        $repo = $this->getRepo();
        $obj  = $repo->addMapper('bad', new BadMapper());
    }

    protected function save($name = 'Test') {
        $repo = $this->getRepo();
        $repo->addMapper('Course', new CourseMapper);

        $course       = new Course();
        $course->name = $name;

        $course = $repo->save('Course', $course);

        $this->assertEquals(
            $name,
            $course->name
        );

        $this->assertIsInt(key($repo->storage['Course']));

        return $course->course_id;
    }

    protected function getRepo() {
        return new class extends Repository {
            public array $storage = [];
        };
    }
}

class BadMapper extends \DealNews\DataMapper\AbstractMapper {
    public function load($id): ?object {
    }

    public function loadMulti(array $ids): ?array {
    }

    public function find(array $filter): ?array {
    }

    public function save(object $object): object {
    }

    public function delete($id): bool {
    }
}
