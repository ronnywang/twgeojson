<?php

// 將 geojson 精細度下調以減少檔案大小的 script
// 用法 php simplify.php [geojson.json] [精細度Ex: 0.0001] > [newgeojson.json]

ini_set('memory_limit', '4096m');
// from https://github.com/AKeN/simplify-php
function simplify($points, $tolerance = 1, $highestQuality = false) {
        if (count($points) < 2) return $points;
        $sqTolerance = $tolerance * $tolerance;
        if (!$highestQuality) {
                $points = simplifyRadialDistance($points, $sqTolerance);
        }
        $points = simplifyDouglasPeucker($points, $sqTolerance);

        return $points;
}


function getSquareDistance($p1, $p2) {
        $dx = $p1['x'] - $p2['x'];
        $dy = $p1['y'] - $p2['y'];
        return $dx * $dx + $dy * $dy;
}


function getSquareSegmentDistance($p, $p1, $p2) {
        $x = $p1['x'];
        $y = $p1['y'];

        $dx = $p2['x'] - $x;
        $dy = $p2['y'] - $y;

        if ($dx !== 0 || $dy !== 0) {

                $t = (($p['x'] - $x) * $dx + ($p['y'] - $y) * $dy) / ($dx * $dx + $dy * $dy);

                if ($t > 1) {
                        $x = $p2['x'];
                        $y = $p2['y'];

                } else if ($t > 0) {
                        $x += $dx * $t;
                        $y += $dy * $t;
                }
        }

        $dx = $p['x'] - $x;
        $dy = $p['y'] - $y;

        return $dx * $dx + $dy * $dy;
}


function simplifyRadialDistance($points, $sqTolerance) { // distance-based simplification
        
        $len = count($points);
        $prevPoint = $points[0];
        $newPoints = array($prevPoint);
        $point = null;
        

        for ($i = 1; $i < $len; $i++) {
                $point = $points[$i];

                if (getSquareDistance($point, $prevPoint) > $sqTolerance) {
                        array_push($newPoints, $point);
                        $prevPoint = $point;
                }
        }

        if ($prevPoint !== $point) {
                array_push($newPoints, $point);
        }

        return $newPoints;
}


// simplification using optimized Douglas-Peucker algorithm with recursion elimination
function simplifyDouglasPeucker($points, $sqTolerance) {

        $len = count($points);

        $markers = array_fill ( 0 , $len - 1, null);
        $first = 0;
        $last = $len - 1;

        $firstStack = array();
        $lastStack  = array();

        $newPoints  = array();

        $markers[$first] = $markers[$last] = 1;

        while ($last) {

                $maxSqDist = 0;

                for ($i = $first + 1; $i < $last; $i++) {
                        $sqDist = getSquareSegmentDistance($points[$i], $points[$first], $points[$last]);

                        if ($sqDist > $maxSqDist) {
                                $index = $i;
                                $maxSqDist = $sqDist;
                        }
                }

                if ($maxSqDist > $sqTolerance) {
                        $markers[$index] = 1;

                        array_push($firstStack, $first);
                        array_push($lastStack, $index);

                        array_push($firstStack, $index);
                        array_push($lastStack, $last);
                }

                $first = array_pop($firstStack);
                $last = array_pop($lastStack);
        }

        for ($i = 0; $i < $len; $i++) {
                if ($markers[$i]) {
                        array_push($newPoints, $points[$i]);
                }
        }

        return $newPoints;
}

$tolerance = array_key_exists(2, $_SERVER['argv']) ? floatval($_SERVER['argv'][2]) : 0.001;

$json = json_decode(file_get_contents($_SERVER['argv'][1]));
foreach ($json->features as $feature_id => $feature) {
    $geometry = $feature->geometry;
    if ($geometry->type == 'MultiPolygon') {
        foreach ($geometry->coordinates as $coordinate_id => $polygons) {
            foreach ($polygons as $polygon_id => $points) {
                $tmp_points = array();
                foreach ($points as $point) {
                    $tmp_points[] = array('x' => $point[0], 'y' => $point[1]);
                }
                $simplify_points = @simplify($tmp_points, $tolerance, true);
                $simplify_polygon = array();
                foreach ($simplify_points as $point) {
                    $simplify_polygon[] = array($point['x'], $point['y']);
                }
                $json->features[$feature_id]->geometry->coordinates[$coordinate_id][$polygon_id] = $simplify_polygon;
            }
        }
    } else {
        throw new Exception('test');
    }

}

echo json_encode($json, JSON_UNESCAPED_UNICODE);
