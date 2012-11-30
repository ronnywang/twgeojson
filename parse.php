<?php

ini_set('memory_limit', '2048m');

class Parser
{
    public function fetchByte($size)
    {
        $ret = substr($this->data, 0, $size);
        $this->data = substr($this->data, $size);
        return $ret;
    }

    /**
     * 把 Well-Known-Binary 轉換成 GeoJSON，程式簡化自 https://github.com/phayes/geoPHP
     *
     * @param string $value 16進位字串
     * @access public
     * @return Object
     */
    public function parseWKB($value)
    {
        $this->data = pack('H*', $value);

        return $this->getGeometry();
    }

    public function getGeometry()
    {
        $base_info = unpack("corder/ctype/cz/cm/cs", $this->fetchByte(5));

        $ret = new StdClass;
        switch ($base_info['type']) {
        case 1:
            $ret->type = 'Point';
            $ret->coordinates = $this->getPoint();
            break;
        case 2:
            $ret->type = 'LineString';
            $ret->coordinates = $this->getLinstring();
            break;
        case 3:
            $ret->type = 'Polygon';
            $ret->coordinates = $this->getPolygon();
            break;
        case 4:
            $ret->type = 'MultiPoint';
            $ret->coordinates = $this->getMulti('point');
            break;
        case 5:
            $ret->type = 'MultiLineString';
            $ret->coordinates = $this->getMulti('line');
            break;
        case 6:
            $ret->type = 'MultiPolygon';
            $ret->coordinates = $this->getMulti('polygon');
            break;
        case 7:
            $ret->type = 'GeometryCollection';
            $ret->coordinates = $this->getMulti('geometry');
            break;
        }
        return $ret;
    }

    public function getPoint() {
        $point_coords = unpack("d*", $this->fetchByte(32));
        return [$point_coords[1], $point_coords[2]];
    }

    public function getLinstring() {
        $line_length = unpack('L', $this->fetchByte(4));

        if (!$line_length[1]) {
            return [];
        }

        $line_coords = unpack('d*', $this->fetchByte($line_length[1] * 32));

        $components = array();
        $i = 1;
        $num_coords = count($line_coords);
        while ($i <= $num_coords) {
            $components[] = [$line_coords[$i], $line_coords[$i + 1]];
            $i += 4;
        }
        return $components;
    }

    public function getPolygon() {
        $poly_length = unpack('L', $this->fetchByte(4));

        $components = array();
        $i = 1;
        while ($i <= $poly_length[1]) {
            $components[] = $this->getLinstring();
            $i++;
        }
        return $components;
    }

    public function getMulti($type) {
        $multi_length = unpack('L', $this->fetchByte(4));

        $components = array();
        $i = 1;
        while ($i <= $multi_length[1]) {
            if ('geometry' == $type) {
                $components[] = $this->getGeometry();
            } else {
                $components[] = $this->getGeometry()->coordinates;
            }
            $i++;
        }

        return $components;
    }

    public function combine($columns, $values)
    {
        $numeric = ['area', 'max_x', 'max_y', 'min_x', 'min_y', 'x', 'y', 'sort', 'shape_leng', 'shape_le_1', 'shape_area'];
        $int = ['objectid', 'oorig_fid'];

        $obj = new StdClass;
        foreach (array_combine($columns, $values) as $k => $v) {
            if (in_array($k, $numeric)) {
                $obj->{$k} = doubleval($v);
            } elseif (in_array($k, $int)) {
                $obj->{$k} = intval($v);
            } elseif ('the_geom' == $k) {
                $obj->{$k} = $this->parseWKB($v);
            } else {
                $obj->{$k} = $v;
            }
        }
        return $obj;
    }

    public function main($file)
    {
        $fp = popen('shp2pgsql -W big5-2003 ' . escapeshellarg($file), 'r');
        $villages = [];
        while (false !== ($line = fgets($fp))) {
            if (!preg_match('#^INSERT INTO "[^"]*" \(([^)]*)\) VALUES \((.*)\)#', $line, $matches)) {
                continue;
            }
            $columns = array_map(function($r) {
                return trim($r, '"');
            }, explode(',', $matches[1]));
            $values = array_map(function($r) {
                return NULL == $r ? null : trim($r, '\'');
            }, explode(',', $matches[2]));
            $village = $this->combine($columns, $values);
            $villages[] = $village;
        }
        $json = new StdClass;
        $json->link = 'https://github.com/ronnywang/twgeojson';
        $json->data_time = '2012-11-13';
        $json->data_source = 'http://tgos.nat.gov.tw/tgos/Web/Metadata/TGOS_MetaData_View.aspx?MID=36646&SHOW_BACK_BUTTON=false';
        $json->description = '全國村里界圖（台澎金馬）-經緯度';
        $json->data = $villages;

        file_put_contents('output.json', json_encode($json, JSON_UNESCAPED_UNICODE));
        $json->data = array_slice($json->data, 0, 10);
        file_put_contents('output.sample.json', json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}

if (!$file = $_SERVER['argv'][1]) {
    die("請用 parseshp.php [file.shp]\n");
}
$parser = new Parser;
$parser->main($file);
