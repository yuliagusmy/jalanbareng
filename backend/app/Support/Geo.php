<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class Geo
{
    public static function usesPostgis(): bool
    {
        return DB::connection()->getDriverName() === 'pgsql';
    }

    public static function pointWkt(float $lng, float $lat): string
    {
        return sprintf('POINT(%s %s)', $lng, $lat);
    }

    public static function setPoint(string $table, string $column, int $id, float $lng, float $lat): void
    {
        if (self::usesPostgis()) {
            DB::statement(
                "UPDATE {$table} SET {$column} = ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography WHERE id = ?",
                [$lng, $lat, $id]
            );

            return;
        }

        DB::table($table)->where('id', $id)->update([
            $column => self::pointWkt($lng, $lat),
        ]);
    }

    public static function setLineString(string $table, string $column, int $id, ?string $wkt): void
    {
        if (self::usesPostgis()) {
            if ($wkt === null) {
                DB::statement("UPDATE {$table} SET {$column} = NULL WHERE id = ?", [$id]);

                return;
            }

            DB::statement(
                "UPDATE {$table} SET {$column} = ST_SetSRID(ST_GeomFromText(?), 4326)::geography WHERE id = ?",
                [$wkt, $id]
            );

            return;
        }

        DB::table($table)->where('id', $id)->update([$column => $wkt]);
    }

    public static function parsePoint(?string $wkt): ?array
    {
        if (! $wkt || ! preg_match('/POINT\(([^ ]+) ([^ ]+)\)/', $wkt, $matches)) {
            return null;
        }

        return [
            'lng' => (float) $matches[1],
            'lat' => (float) $matches[2],
        ];
    }

    public static function parseLineString(?string $wkt): array
    {
        if (! $wkt || ! preg_match('/LINESTRING\((.+)\)/', $wkt, $matches)) {
            return [];
        }

        return collect(explode(',', $matches[1]))->map(function ($point) {
            $coords = explode(' ', trim($point));
            if (count($coords) !== 2) {
                return null;
            }

            return ['lng' => (float) $coords[0], 'lat' => (float) $coords[1]];
        })->filter()->values()->all();
    }

    public static function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $earth * asin(min(1, sqrt($a)));
    }
}
