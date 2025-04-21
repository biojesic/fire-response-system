<?php

namespace App\Models;

class AssignedIncident
{
    public $firefighter_id;
    public $location;
    public $landmark;
    public $timeReported;
    public $teamName;
    public $teamLeader;
    public $latitude;
    public $longitude;

    public function __construct($firefighter_id, $location, $landmark, $timeReported, $teamName, $teamLeader, $latitude, $longitude)
    {
        $this->firefighter_id = $firefighter_id;
        $this->location = $location;
        $this->landmark = $landmark;
        $this->timeReported = $timeReported;
        $this->teamName = $teamName;
        $this->teamLeader = $teamLeader;
        $this->teamLeader = $latitude;
        $this->teamLeader = $longitude;
    }

    // Factory to create from API Response (optional if you use this in API Controller)
    public static function fromArray($data)
    {
        return new self(
            $data['firefighter_id'],
            $data['location'],
            $data['landmark'],
            $data['created_at'],
            $data['teamName'],
            $data['Team Leader']
            $data['latitude']
            $data['longitude']
        );
    }
}

