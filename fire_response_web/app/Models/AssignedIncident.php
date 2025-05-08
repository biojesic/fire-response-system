<?php

namespace App\Models;

class AssignedIncident
{
    public $firefighter_id;
    public $location;
    public $landmark;
    public $status;
    public $timeReported;
    public $teamName;
    public $teamLeader;
    public $latitude;
    public $longitude;
    public $fireReportId;

    public function __construct($firefighter_id, $location, $landmark, $status, $timeReported, $teamName, $teamLeader, $latitude, $longitude, $fireReportId)
    {
        $this->firefighter_id = $firefighter_id;
        $this->location = $location;
        $this->landmark = $landmark;
        $this->status = $status;
        $this->timeReported = $timeReported;
        $this->teamName = $teamName;
        $this->teamLeader = $teamLeader;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->fireReportId = $fireReportId;
    }

    // Factory to create from API Response (optional if you use this in API Controller)
    public static function fromArray($data)
    {
        return new self(
            $data['firefighter_id'],
            $data['location'],
            $data['landmark'],
            $data['status'],
            $data['created_at'],
            $data['teamName'],
            $data['teamLeader'],
            $data['latitude'],
            $data['longitude'],
            $data['fire_report_id']
        );
    }
}

