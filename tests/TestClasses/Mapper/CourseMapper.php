<?php

namespace DealNews\DataMapper\Tests\TestClasses\Mapper;

/**
 * Test Course Mapper
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     DataMapper
 */
class CourseMapper extends \DealNews\DataMapper\AbstractMapper {
    public const MAPPED_CLASS = "\DealNews\DataMapper\Tests\TestClasses\Course";

    public const PRIMARY_KEY = 'course_id';

    public const MAPPING = [
        'course_id'   => [],
        'name'        => [
            'constraint' => [
                'min' => 1,
                'max' => 100,
            ],
        ],
        'active' => [],
        'teacher'    => [
            'class'       => \DealNews\DataMapper\Tests\TestClasses\Teacher::class,
            'one_to_many' => false,
        ],
        'students'    => [
            'class'       => \DealNews\DataMapper\Tests\TestClasses\Student::class,
            'one_to_many' => true,
            'constraint'  => [
                'type' => 'array',
            ],
        ],
        'create_datetime' => [
            'rename'    => 'create_date',
            'read_only' => true,
        ],
    ];

    public function testSetData(array $data) {
        return $this->setData($data);
    }

    public function testGetData(\DealNews\DataMapper\Tests\TestClasses\Course $course) {
        return $this->getData($course);
    }

    protected static $data = [];

    public function load($id): ?object {
        $ret = false;
        if (!empty(self::$data[$id])) {
            $ret = self::$data[$id];
        }

        return $ret;
    }

    public function loadMulti(array $ids): ?array {
        $ret = [];
        foreach ($ids as $id) {
            if (!empty(self::$data[$id])) {
                $ret[$id] = self::$data[$id];
            }
        }

        return $ret;
    }

    public function find(array $filter): ?array {
        $ret = [];
        foreach (self::$data as $id => $obj) {
            $match = true;
            foreach ($filter as $prop => $value) {
                if ($obj->$prop != $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $ret[$id] = $obj;
            }
        }

        return $ret;
    }

    public function save(object $object): object {
        self::$data[$object->course_id] = $object;

        return $object;
    }

    public function delete($id): bool {
        if (!empty(self::$data[$id])) {
            unset(self::$data[$id]);
        }

        return true;
    }
}
