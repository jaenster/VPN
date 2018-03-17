<?php

namespace Rawsocket\Pcap;


class DumpablePacket
{
    private $data;
    const BYTES_PER_COLUMN = 8;
    const COLUMNS_PER_ROW = 2;
    const BYTES_PER_ROW = 16;

    public function __toString()
    {
        return $this->dumpPacketData();
    }

    private function dumpPacketData()
    {
        $data = $this->data;
        $ret = '';
        $total = strlen($data);
        $totalRows = ceil($total / self::BYTES_PER_ROW);
        for ($row = 0, $offset = 0; $row < $totalRows; $row++) {
            $ret .= $this->startwith.str_pad($row * self::BYTES_PER_ROW, 5, "0", STR_PAD_LEFT);
            $subdata = substr($data, $offset, self::BYTES_PER_ROW);
            $ret .= $this->dumpHex($subdata);
            $ret .= $this->dumpAscii($subdata);
            $offset += self::BYTES_PER_ROW;
            $ret .= "\n";
        }
        $ret .= "\n";
        return $ret;
    }

    private function dumpHex($data)
    {
        $ret = '';
        // Write N columas with the hex value of what we sniffed
        for ($column = 0; $column < self::BYTES_PER_ROW; $column++) {
            if (($column % self::BYTES_PER_COLUMN) == 0) {
                $ret .= "   ";
            }
            if (!isset($data[$column])) {
                $ret .= '   ';
            } else {
                $value = $data[$column];
                $ordValue = ord($value);
                $ret .= str_pad(sprintf("%x", $ordValue), 2, "0", STR_PAD_LEFT) . " ";
            }
        }
        return $ret;
    }

    private function dumpAscii($data)
    {
        $ret = '';
        // Write a column with the ASCII of what we sniffed
        for ($column = 0; $column < self::BYTES_PER_ROW; $column++) {
            if (($column % self::BYTES_PER_COLUMN) == 0) {
                $ret .= "   ";
            }
            if (!isset($data[$column])) {
                $ret .= ' ';
            } else {
                $value = $data[$column];
                $ordValue = ord($value);
                if ($ordValue > 31 && $ordValue < 128) {
                    $ret .= $data[$column];
                } else {
                    $ret .= ".";
                }
            }
        }
        return $ret;
    }

    public function __construct(string $data,$startwith='')
    {
        $this->data = $data;
        if ($startwith != '')
        {
            $startwith .= ' ';
        }
        $this->startwith = $startwith;
    }
}