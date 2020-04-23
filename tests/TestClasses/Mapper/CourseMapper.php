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
        'course_id' => [],
        'name'      => [],
    ];

    public function testSetData(array $data) {
        return $this->setData($data);
    }

    public function testGetData(\DealNews\DataMapper\Tests\TestClasses\Course $course) {
        return $this->getData($course);
    }

    protected static $data = [];

    public function load($id) {
        $ret = false;
        if (!empty(self::$data[$id])) {
            $ret = self::$data[$id];
        }

        return $ret;
    }

    public function loadMulti(array $ids) {
        $ret = [];
        foreach ($ids as $id) {
            if (!empty(self::$data[$id])) {
                $ret[$id] = self::$data[$id];
            }
        }

        return $ret;
    }

    public function find(array $filter) {
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

    public function save($object) {
        self::$data[$object->course_id] = $object;

        return $object;
    }

    public function delete($id) {
        if (!empty(self::$data[$id])) {
            unset(self::$data[$id]);
        }

        return true;
    }
}
