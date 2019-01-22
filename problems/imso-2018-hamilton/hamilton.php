<?php
/**
 * Manage all points and their relation.
 */
class PointSet
{
    /**
     * List of points.
     * @var string[]
     */
    private $points;

    /**
     * Relation between points (list of connnected points for each point).
     * @var string[][]
     */
    private $pointsRelation;

    /**
     * @var int
     */
    private $pointNum;

    public function __construct($initialPointRelations)
    {
        $this->points = [];
        $this->pointsRelation = [];

        foreach ($initialPointRelations as $point1 => $relation) {
            foreach ($relation as $point2) {
                $this->addPointRelation($point1, $point2);
                $this->addPointRelation($point2, $point1);
            }
        }
        $this->pointNum = count($this->points);
    }

    private function addPointRelation($point1, $point2)
    {
        if (!isset($this->pointsRelation[$point1])) {
            $this->points[$point1] = $point1;
            $this->pointsRelation[$point1] = [];
        }
        $this->pointsRelation[$point1][$point2] = $point2;
    }

    private $startPoint;
    private $countPath;
    private $currentPath;
    private $passedPoints;

    public function findRoute($pointName)
    {
        $this->startPoint = $pointName;
        $this->countPath = 0;
        $this->currentPath = [];
        $this->passedPoints = [];
        $this->findRouteFrom($pointName, 1);
    }

    private function findRouteFrom($point, $level)
    {
        $this->currentPath[$level] = $point;
        $this->passedPoints[$point] = true;
        // Check relative points.
        foreach ($this->pointsRelation[$point] as $nextPoint) {
            // Check if return back to start point.
            if ($nextPoint == $this->startPoint) { // Touch to start point.
                if ($level == $this->pointNum) { // Went through all points.
                    $this->routeFound();
                }
            } else { // Go to next point.
                if (!isset($this->passedPoints[$nextPoint])) { // Not passed this point.
                    $this->passedPoints[$nextPoint] = true;

                    $this->findRouteFrom($nextPoint, $level + 1);

                    unset($this->passedPoints[$nextPoint]);
                }
            }
        }
        unset($this->passedPoints[$point]);
    }

    private function routeFound()
    {
        $this->countPath++;
        $pathStr = join(' -> ', array_merge($this->currentPath, [$this->startPoint]));
        echo "{$this->countPath}. $pathStr\n";
    }
}

$initialPointRelations = [
    'A' => ['C', 'D', 'E', 'F'],
    'B' => ['C', 'D', 'E', 'F'],
    'C' => ['D', 'F'],
    'D' => ['E'],
    'E' => ['F'],
];

$pointSet = new PointSet($initialPointRelations);
$pointSet->findRoute('A');