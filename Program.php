<?php

/**
 * Program class
 *
 * PHP version 8
 *
 * LICENSE: This source is released under the MIT license.
 *
 * @category  Interface
 * @package   ObsFromPlano
 * @author    James Shields <james@lostcarpark.com>
 * @copyright 2024 James Shields
 * @license   https://opensource.org/license/mit MIT license
 * @link      https://github.com/glasgow2024/obs_from_plano
 */

require_once 'People.php';

/**
 * Class for reading program data and outputting OBS room files.
 */
class Program
{

    protected array $programData;

    /**
     * Constructor for Program.
     *
     * @param string $fileName The filename of the program data file.
     * @param People $people   Object for looking up person data.
     */
    public function __construct(string $fileName, protected People $people)
    {
        $json = file_get_contents($fileName);
        $this->programData = json_decode($json);
    }

    /**
     * Combine elements of location to make room name.
     *
     * @param array $loc Array with room name parts.
     *
     * @return string
     */
    protected function makeRoomName(array $loc): string
    {
        return implode(' - ', $loc);
    }

    /**
     * Remove spaces and convert to lowercase to make filename.
     *
     * @param string $roomName The room name as a string.
     *
     * @return string
     */
    protected function makeFileName(string $roomName): string
    {
        return strtolower(str_replace(' ', '', $roomName));
    }

    /**
     * Get an array containing room names, indexed by lowercase name with spaces
     * removed.
     *
     * @return array
     */
    public function getLocations(): array
    {
        $locs = [];
        foreach ($this->programData as $programItem) {
            $roomName = $this->makeRoomName($programItem->loc);
            $fileName = $this->makeFileName($roomName);
            if (!array_key_exists($fileName, $locs)) {
                $locs[$fileName] = $roomName;
            }
        }
        ksort($locs);
        return $locs;
    }

    /**
     * Function to strip out HTML tags and replace quote characters with curley quotes.
     *
     * @param string $text The raw text to strip.
     *
     * @return string
     */

    public function stripHtml(
        string|null $text
    ): string {
        if (is_null($text)) {
            return '';
        }
        $text = strip_tags(str_replace('&#39;', '’', $text));
        return $text;
    }

    /**
     * Function to write all future items in a room to a JSON file.
     *
     * @param string   $fileName The file name to save to.
     * @param string   $roomName The display name of the room.
     * @param DateTime $timeNow  The current time for filtering past items.
     *
     * @return void
     */
    public function writeObsRoomFile(
        string $fileName,
        string $roomName,
        DateTime $timeNow
    ): void {
        $items = [];
        $timeZone = new DateTimeZone('Europe/London');
        foreach ($this->programData as $programItem) {
            $dateTime = new DateTime($programItem->datetime);
            $midpointInSeconds = $programItem->mins * 30;
            $interval = new DateInterval('PT' . $midpointInSeconds . 'S');
            if ($this->makeRoomName($programItem->loc) == $roomName
                && $dateTime->add($interval) > $timeNow
            ) {
                $dateTime->setTimezone($timeZone);
                $itemPeople = [];
                foreach ($programItem->people as $person) {
                    $itemPeople[] = $this->people->getPerson($person->id);
                }
                $items[] = [
                'id' => $programItem->id,
                'title' => $programItem->title,
                'tags' => $programItem->tags,
                'day' => $dateTime->format('l'),
                'date' => $dateTime->format('Y-m-d'),
                'time' => $dateTime->format('H:i'),
                'mins' => $programItem->mins,
                'loc' => [$roomName],
                'desc' => $this->stripHtml($programItem->desc),
                'people' => $itemPeople,
                'links' => $programItem->links ?? [],
                ];
            }
        }
        file_put_contents($fileName, json_encode($items, JSON_UNESCAPED_UNICODE));
    }
}
