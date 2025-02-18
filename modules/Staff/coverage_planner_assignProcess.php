<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Domain\Staff\StaffCoverageGateway;
use Gibbon\Data\Validator;
use Gibbon\Services\Format;

require_once '../../gibbon.php';

$_POST = $container->get(Validator::class)->sanitize($_POST);

$gibbonStaffCoverageID = $_POST['gibbonStaffCoverageID'] ?? '';
$gibbonStaffCoverageDateID = $_POST['gibbonStaffCoverageDateID'] ?? '';
$gibbonPersonIDCoverage = $_POST['gibbonPersonIDCoverage'] ?? '';
$date = $_POST['date'] ?? '';

$URL = $session->get('absoluteURL').'/index.php?q=/modules/Staff/coverage_planner.php&sidebar=true&date='.Format::date($date);

if (isActionAccessible($guid, $connection2, '/modules/Staff/coverage_manage.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
} elseif (empty($gibbonStaffCoverageID)) {
    $URL .= '&return=error1';
    header("Location: {$URL}");
    exit;
} else {
    // Proceed!
    $staffCoverageGateway = $container->get(StaffCoverageGateway::class);
    $coverage = $staffCoverageGateway->getByID($gibbonStaffCoverageID);

    if (empty($coverage) || empty($gibbonPersonIDCoverage)) {
        $URL .= '&return=error2';
        header("Location: {$URL}");
        exit;
    }

    $data = [
        'gibbonPersonIDCoverage' => $gibbonPersonIDCoverage,
        'gibbonPersonIDStatus'   => $gibbon->session->get('gibbonPersonID'),
        'requestType'            => 'Assigned',
        'status'                 => 'Accepted',
        'notificationSent'       => 'N',
    ];

    // Update the coverage
    $updated = $staffCoverageGateway->update($gibbonStaffCoverageID, $data);

    $URL .= !$updated
        ? '&return=error2'
        : '&return=success0';

    header("Location: {$URL}");
}
