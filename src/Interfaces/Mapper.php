<?php

namespace DealNews\DataMapper\Interfaces;

/**
 * Maps an object to a storage system
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     DataMapper
 */
interface Mapper {
    /**
     * Loads an object from the storage system
     *
     * @param  int|string    $id Primay key id of the object to load
     * @return null|object
     * @throws \Error
     */
    public function load($id): ?object;

    /**
     * Loads multiple objects from the storage system
     *
     * @param  array    $ids Array of primay key ids of the objects to load
     * @return null|array
     * @throws \Error
     */
    public function loadMulti(array $ids): ?array;

    /**
     * Finds multiple objects in the storage system
     *
     * @param  array    $filter  Array of filters where the keys are column
     *                           names and the values are column values to
     *                           filter upon.
     * @param int|null  $limit   Number of matches to return
     * @param int|null  $start   Start position
     * @param string    $order   The order of returned matches
     *
     * @return null|array
     * @throws \Error
     */
    public function find(array $filter, ?int $limit = null, ?int $start = null, string $order = ''): ?array;

    /**
     * Saves the object to the storage system
     *
     * @param  object $object
     * @return object
     * @throws \Error
     */
    public function save(object $object): object;

    /**
     * Deletes an object from the storage system
     *
     * @param  int|string    $id Primay key id of the object to delete
     * @return boolean
     * @throws \Error
     */
    public function delete($id): bool;

    /**
     * Returns the name of the class the mapper saves/loads
     *
     * @return string
     */
    public static function getMappedClass(): string;
}
