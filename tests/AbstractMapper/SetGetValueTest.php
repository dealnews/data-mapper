<?php

namespace DealNews\DataMapper\Tests\AbstractMapper;

use DealNews\DataMapper\AbstractMapper;
use PHPUnit\Framework\Attributes\DataProvider;

class SetGetValueTest extends \PHPUnit\Framework\TestCase {
    public function testSetInvalidEncoding() {
        $this->expectException('\\LogicException');
        $obj    = new SetGetValueMock();
        $mapper = new SetGetValueMapperMock();
        $mapper->setValue($obj, 'foo', ['foo' => true], ['encoding' => 'bad']);
    }

    public function testGetInvalidEncoding() {
        $this->expectException('\\LogicException');
        $obj      = new SetGetValueMock();
        $obj->foo = 1;
        $mapper   = new SetGetValueMapperMock();
        $mapper->getValue($obj, 'foo', ['encoding' => 'bad']);
    }

    #[DataProvider('encodingSetData')]
    public function testSetValue($property, $data, $mapping, $expect) {
        $obj    = new SetGetValueMock();
        $mapper = new SetGetValueMapperMock();
        $mapper->setValue($obj, $property, $data, $mapping);
        if (is_object($expect)) {
            $this->assertEquals(
                $expect,
                $obj->$property
            );
        } else {
            $this->assertSame(
                $expect,
                $obj->$property
            );
        }
    }

    public static function encodingSetData() {
        return [

            'Bool True String Yes uses Filter' => [
                'bool',
                [
                    'bool' => 'Yes',
                ],
                [
                ],
                true,
            ],

            'Bool False String No uses Filter' => [
                'bool',
                [
                    'bool' => 'No',
                ],
                [
                ],
                false,
            ],

            'Bool True String AYE uses bool values' => [
                'bool',
                [
                    'bool' => 'AYE',
                ],
                [
                    'bool_values' => [
                        'true'  => 'AYE',
                        'false' => 'NAY',
                    ],
                ],
                true,
            ],

            'Bool False String NAY uses bool values' => [
                'bool',
                [
                    'bool' => 'NAY',
                ],
                [
                    'bool_values' => [
                        'true'  => 'AYE',
                        'false' => 'NAY',
                    ],
                ],
                false,
            ],

            'JSON Array' => [
                'json_array',
                [
                    'json_array' => '{"foo":"bar"}',
                ],
                [
                    'encoding'   => 'json',
                    'json_assoc' => true,
                ],
                [
                    'foo' => 'bar',
                ],
            ],
            'JSON Object' => [
                'json_object',
                [
                    'json_object' => '{"foo":"bar"}',
                ],
                [
                    'encoding' => 'json',
                ],
                (object)[
                    'foo' => 'bar',
                ],
            ],
            'YAML' => [
                'yaml',
                [
                    'yaml' => yaml_emit(['foo' => 'bar']),
                ],
                [
                    'encoding' => 'yaml',
                ],
                [
                    'foo' => 'bar',
                ],
            ],
            'Serialize' => [
                'class',
                [
                    'class' => serialize(new \stdClass()),
                ],
                [
                    'encoding' => 'serialize',
                ],
                new \stdClass(),
            ],
            'DateTime' => [
                'dt',
                [
                    'dt' => '2020-01-01 12:00:00',
                ],
                [
                    'class' => '\\DateTime',
                ],
                new \DateTime('2020-01-01 12:00:00'),
            ],
            'DateTimeImmutable' => [
                'dti',
                [
                    'dti' => '2020-01-01 12:00:00',
                ],
                [
                    'class' => '\\DateTimeImmutable',
                ],
                new \DateTimeImmutable('2020-01-01 12:00:00'),
            ],
            'DateTime as Int' => [
                'dt',
                [
                    'dt' => strtotime('2020-01-01 12:00:00'),
                ],
                [
                    'class' => '\\DateTime',
                ],
                new \DateTime('2020-01-01 12:00:00'),
            ],
            'ArrayObject' => [
                'arr',
                [
                    'arr' => [1, 2, 3],
                ],
                [],
                new \ArrayObject([1, 2, 3]),
            ],
        ];
    }

    #[DataProvider('encodingGetData')]
    public function testGetValue($property, $input, $mapping, $expect) {
        $obj            = new SetGetValueMock();
        $mapper         = new SetGetValueMapperMock();
        $obj->$property = $input;
        $value          = $mapper->getValue($obj, $property, $mapping);
        $this->assertSame(
            $expect,
            $value
        );
    }

    public static function encodingGetData() {
        return [

            'Bool True String Yes' => [
                'bool',
                true,
                [
                    'bool_values' => [
                        'true'  => 'Yes',
                        'false' => 'No',
                    ],
                ],
                'Yes',
            ],

            'Bool False String No' => [
                'bool',
                false,
                [
                    'bool_values' => [
                        'true'  => 'Yes',
                        'false' => 'No',
                    ],
                ],
                'No',
            ],

            'JSON Array' => [
                'json_array',
                [
                    'foo' => 'bar',
                ],
                [
                    'encoding'   => 'json',
                    'json_assoc' => true,
                ],
                '{"foo":"bar"}',
            ],
            'JSON Object' => [
                'json_object',
                (object)[
                    'foo' => 'bar',
                ],
                [
                    'encoding' => 'json',
                ],
                '{"foo":"bar"}',
            ],
            'YAML' => [
                'yaml',
                [
                    'foo' => 'bar',
                ],
                [
                    'encoding' => 'yaml',
                ],
                yaml_emit(['foo' => 'bar']),
            ],
            'Serialize' => [
                'class',
                new \stdClass(),
                [
                    'encoding' => 'serialize',
                ],
                serialize(new \stdClass()),
            ],
            'DateTime' => [
                'dt',
                new \DateTime('2020-01-01 12:00:00'),
                [
                    'class' => '\\DateTime',
                ],
                '2020-01-01 12:00:00',
            ],
            'DateTimeImmutable' => [
                'dti',
                new \DateTimeImmutable('2020-01-01 12:00:00'),
                [
                    'class' => '\\DateTimeImmutable',
                ],
                '2020-01-01 12:00:00',
            ],
            'DateTime as Int' => [
                'dt',
                new \DateTime('2020-01-01 12:00:00'),
                [
                    'class' => '\\DateTime',
                    'type'  => 'int',
                ],
                strtotime('2020-01-01 12:00:00'),
            ],
            'ArrayObject' => [
                'arr',
                new \ArrayObject([1, 2, 3]),
                [],
                [1, 2, 3],
            ],
        ];
    }
}

class SetGetValueMock {
    public int $int          = 0;
    public string $string    = '';
    public bool $bool        = false;
    public float $float      = 0.0;
    public array $json_array = [];
    public \stdClass $json_object;
    public array $yaml = [];
    public \stdClass $class;
    public \DateTime $dt;
    public \DateTimeImmutable $dti;
    public \ArrayObject $arr;
    public bool $foo = false;

    public function __construct() {
        $this->json_object = new \stdClass();
        $this->class       = new \stdClass();
        $this->dt          = new \DateTime();
        $this->dti         = new \DateTimeImmutable();
        $this->arr         = new \ArrayObject();
    }
}

class SetGetValueMapperMock extends AbstractMapper {
    public function setValue(object $object, string $property, array $data, array $mapping) {
        return parent::setValue($object, $property, $data, $mapping);
    }

    public function getValue(object $object, string $property, array $mapping) {
        return parent::getValue($object, $property, $mapping);
    }

    public function load($id): ?object {
        return null;
    }

    public function delete($id): bool {
        return true;
    }

    public function find(array $filter, ?int $limit = null, ?int $start = null, string $order = ''): ?array {
        return null;
    }

    public function loadMulti(array $ids): ?array {
        return null;
    }

    public function save(object $object): object {
        return $object;
    }
}
