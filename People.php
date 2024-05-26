<?php

/**
 * People class
 *
 * PHP version 8
 *
 * LICENSE: This source is released under the MIT license.
 *
 * @category  Interface
 * @package   Obs_From_Plano
 * @author    James Shields <james@lostcarpark.com>
 * @copyright 2024 James Shields
 * @license   https://opensource.org/license/mit MIT license
 * @link      https://github.com/glasgow2024/obs_from_plano
 */

/**
 * Class for reading person data file, and indexing by person ID.
 */
class People
{

    /**
     * Array of people indexed by ID.
     *
     * @var array
     */
    protected array $peopleData = [];

    /**
     * Constructor for people. Reads data file and builds indexed array.
     *
     * @param string $fileName Filename of the person data file.
     */
    public function __construct(string $fileName)
    {
        $json = file_get_contents($fileName);
        $people = json_decode($json);
        foreach ($people as $person) {
            $this->peopleData[$person->id] = $person;
        }
    }

    /**
     * Lookup a person by their ID and return the key data fields.
     *
     * @param string $key The ID of the person to return.
     *
     * @return stdClass
     */
    public function getPerson(string $key): stdClass
    {
        $personDetails = $this->peopleData[$key];
        return (object)[
        'id' => $personDetails->id,
        'name' => $personDetails->name,
        ];
    }
}
